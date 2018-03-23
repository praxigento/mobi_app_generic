<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Init;

use Magento\Tax\Model\Calculation\Rate as EntityTaxRate;
use Magento\Tax\Model\Calculation\Rule as EntityTaxRule;
use Praxigento\App\Generic2\Config as Cfg;

class Stocks
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /**#@+
     * IDs for groups (stores in adminhtml terms).
     */
    const DEF_GROUP_ID_ADMIN = 0;
    const DEF_GROUP_ID_BALTIC = self::DEF_GROUP_ID_MAIN;
    const DEF_GROUP_ID_MAIN = 1;
    const DEF_GROUP_ID_RUSSIAN = 2;
    /**#@-  */

    /**#@+
     * IDs for stocks (warehouses).
     */
    const DEF_STOCK_ID_BALTIC = self::DEF_STOCK_ID_DEFAULT;
    const DEF_STOCK_ID_DEFAULT = 1;
    const DEF_STOCK_ID_RUSSIAN = 2;
    /**#@-  */

    /**#@+
     * IDs for stores (store views in adminhtml terms).
     */
    const DEF_STORE_ID_ADMIN = 0;
    const DEF_STORE_ID_BALTIC_EN = self::DEF_STORE_ID_DEFAULT;
    const DEF_STORE_ID_BALTIC_RU = 2;
    const DEF_STORE_ID_DEFAULT = 1;
    const DEF_STORE_ID_RUSSIAN_RU = 3;
    /**#@-  */

    /**#@+
     * Odoo IDs for warehouses.
     */
    const DEF_WRHS_ODOO_ID_BALTIC = 2;
    const DEF_WRHS_ODOO_ID_RUSSIAN = 3;
    /**#@-  */

    /** @var  \Magento\Store\Model\Group */
    private $groupBaltic;
    /** @var  \Magento\Store\Model\Group */
    private $groupRussian;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var  \Magento\Store\Api\GroupRepositoryInterface */
    private $mageRepoGroup;
    /** @var  \Magento\CatalogInventory\Api\StockRepositoryInterface */
    private $mageRepoStock;
    /** @var  \Magento\Store\Api\StoreRepositoryInterface */
    private $mageRepoStore;
    /** @var  \Magento\Framework\Event\ManagerInterface */
    private $manEvent;
    /** @var  \Magento\Store\Model\StoreManager */
    private $manStore;
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var  \Praxigento\Core\App\Repo\IGeneric */
    private $daoGeneric;
    /** @var  \Praxigento\Warehouse\Repo\Dao\Warehouse */
    private $daoWrhs;
    /** @var \Praxigento\Odoo\Repo\Dao\Warehouse */
    private $daoWrhsOdoo;
    /** @var \Magento\Store\Model\Store */
    private $storeBalticEn;
    /** @var \Magento\Store\Model\Store */
    private $storeBalticRu;
    /** @var \Magento\Store\Model\Store */
    private $storeRussianRu;
    /** @var Sub\SalesRules */
    private $subRules;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Magento\Store\Model\StoreManager $manStore,
        \Magento\Framework\Event\ManagerInterface $manEvent,
        \Magento\Store\Api\GroupRepositoryInterface $mageRepoGroup,
        \Magento\Store\Api\StoreRepositoryInterface $mageRepoStore,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $mageRepoStock,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric,
        \Praxigento\Warehouse\Repo\Dao\Warehouse $daoWrhs,
        \Praxigento\Odoo\Repo\Dao\Warehouse $daoWrhsOdoo,
        Sub\SalesRules\Proxy $subRules
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init:stocks',
            'Create sample stores in application and map warehouses/stocks to stores.'
        );
        $this->logger = $logger;
        $this->manTrans = $manTrans;
        $this->manStore = $manStore;
        $this->manEvent = $manEvent;
        $this->mageRepoGroup = $mageRepoGroup;
        $this->mageRepoStore = $mageRepoStore;
        $this->mageRepoStock = $mageRepoStock;
        $this->daoGeneric = $daoGeneric;
        $this->daoWrhs = $daoWrhs;
        $this->daoWrhsOdoo = $daoWrhsOdoo;
        $this->subRules = $subRules;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->checkAreaCode();
        $this->processGroups();
        $this->processStores();
        $this->processStocks();
        $this->processTaxes();
        /* init sales rules */
        $this->subRules->init();
        $output->writeln("Generic store views configuration is completed.");
    }

    private function getTaxCalcs($rateId, $ruleId)
    {
        $entity = Cfg::ENTITY_MAGE_TAX_CALC;
        $where = Cfg::E_TAX_CALC_A_RATE_ID . '=' . (int)$rateId;
        $where .= ' AND ' . Cfg::E_TAX_CALC_A_RULE_ID . '=' . (int)$ruleId;
        $rows = $this->daoGeneric->getEntities($entity, null, $where);
        $result = is_array($rows) && count($rows);
        return $result;
    }

    private function getTaxRateByCode($code)
    {
        $result = null;
        $entity = Cfg::ENTITY_MAGE_TAX_CALC_RATE;
        $cols = [Cfg::E_TAX_CALC_RATE_A_ID];
        $where = EntityTaxRate::KEY_CODE . '=' . $this->daoGeneric->getConnection()->quote($code);
        $rows = $this->daoGeneric->getEntities($entity, $cols, $where);
        if (is_array($rows)) {
            $one = reset($rows);
            $result = $one[Cfg::E_TAX_CALC_RATE_A_ID];
        }
        return $result;
    }

    private function getTaxRuleByCode($code)
    {
        $result = null;
        $entity = Cfg::ENTITY_MAGE_TAX_CALC_RULE;
        $cols = [Cfg::E_TAX_CALC_RULE_A_ID];
        $where = EntityTaxRule::KEY_CODE . '=' . $this->daoGeneric->getConnection()->quote($code);
        $rows = $this->daoGeneric->getEntities($entity, $cols, $where);
        if (is_array($rows)) {
            $one = reset($rows);
            $result = $one[Cfg::E_TAX_CALC_RULE_A_ID];
        }
        return $result;
    }

    /**
     * We cannot create tables in the DB transaction.
     */
    private function initStores()
    {
        /* MOBI-312 : init store view (create sequences tables ) */
        $this->manEvent->dispatch('store_add', ['store' => $this->storeBalticEn]);
        $this->manEvent->dispatch('store_add', ['store' => $this->storeBalticRu]);
        $this->manEvent->dispatch('store_add', ['store' => $this->storeRussianRu]);
        $this->manStore->reinitStores();
    }

    /**
     * Update default group (store) as 'Baltic' and create new group for 'Russian'.
     */
    private function processGroups()
    {
        /* load and update Baltic group (store)*/
        $this->groupBaltic = $this->manObj->create(\Magento\Store\Model\Group::class);
        $this->groupBaltic->load(self::DEF_GROUP_ID_MAIN);
        $rootCatId = $this->groupBaltic->getRootCategoryId();
        $this->groupBaltic->setName('Baltic');
        $this->groupBaltic->save();
        $this->manEvent->dispatch('store_group_save', ['group' => $this->groupBaltic]);
        /* create Russian group (store) */
        $this->groupRussian = $this->manObj->create(\Magento\Store\Model\Group::class);
        $this->groupRussian->load(self::DEF_GROUP_ID_RUSSIAN);
        $this->groupRussian->setName('Russian');
        $this->groupRussian->setWebsiteId(Cfg::DEF_WEBSITE_ID_BASE);
        $this->groupRussian->setRootCategoryId($rootCatId);
        $this->groupRussian->save();
        $this->manEvent->dispatch('store_group_save', ['group' => $this->groupRussian]);
    }

    /**
     * Update default stock (switch to website #1 from website #0), add new stock and 2 warehouses; bind warehouses
     * with stocks.
     */
    private function processStocks()
    {
        /* update default stock as Baltic */
        /** @var \Magento\CatalogInventory\Model\Stock $stockBaltic */
        $stockBaltic = $this->mageRepoStock->get(self::DEF_STOCK_ID_DEFAULT);
        $stockBaltic->setStockName('Baltic');
        $stockBaltic->setWebsiteId(Cfg::DEF_WEBSITE_ID_ADMIN);
        $this->mageRepoStock->save($stockBaltic);
        /* create new stock as Russian */
        try {
            $stockRussian = $this->mageRepoStock->get(self::DEF_STOCK_ID_RUSSIAN);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $stockRussian = $this->manObj->create(\Magento\CatalogInventory\Model\Stock::class);
            // $stockRussian->setStockId(self::DEF_STOCK_ID_RUSSIAN);
        } finally {
            $stockRussian->setStockName('Russian');
            $stockRussian->setWebsiteId(Cfg::DEF_WEBSITE_ID_ADMIN);
            $this->mageRepoStock->save($stockRussian);
        }
        /* create new warehouses */
        /* Baltic */
        $wrhsBalt = $this->daoWrhs->getById(self::DEF_STOCK_ID_BALTIC);
        if (!$wrhsBalt) {
            /* create warehouse itself */
            $wrhsBalt = new \Praxigento\Warehouse\Repo\Data\Warehouse();
            $wrhsBalt->setCode('Baltic');
            $wrhsBalt->setCurrency('EUR');
            $wrhsBalt->setCountryCode('LV');
            $wrhsBalt->setNote('Warehouse for Baltic states (LV, LT, EE)');
            $wrhsBalt->setStockRef(self::DEF_STOCK_ID_BALTIC);
            $this->daoWrhs->create($wrhsBalt);
            /* create link to Odoo warehouse */
            $wrhsBaltOdoo = new \Praxigento\Odoo\Repo\Data\Warehouse();
            $wrhsBaltOdoo->setMageRef(self::DEF_STOCK_ID_BALTIC);
            $wrhsBaltOdoo->setOdooRef(self::DEF_WRHS_ODOO_ID_BALTIC);
            $this->daoWrhsOdoo->create($wrhsBaltOdoo);
        }
        /* Russian */
        $wrhsRus = $this->daoWrhs->getById(self::DEF_STOCK_ID_RUSSIAN);
        if (!$wrhsRus) {
            /* create warehouse itself */
            $wrhsRus = new \Praxigento\Warehouse\Repo\Data\Warehouse();
            $wrhsRus->setCode('Russian');
            $wrhsRus->setCurrency('USD');
            $wrhsRus->setCountryCode('RU');
            $wrhsRus->setNote('Warehouse for Russian Federation');
            $wrhsRus->setStockRef(self::DEF_STOCK_ID_RUSSIAN);
            $this->daoWrhs->create($wrhsRus);
            /* create link to Odoo warehouse */
            $wrhsRusOdoo = new \Praxigento\Odoo\Repo\Data\Warehouse();
            $wrhsRusOdoo->setMageRef(self::DEF_STOCK_ID_RUSSIAN);
            $wrhsRusOdoo->setOdooRef(self::DEF_WRHS_ODOO_ID_RUSSIAN);
            $this->daoWrhsOdoo->create($wrhsRusOdoo);
        }
    }

    /**
     * Update default store (store view) for Baltic group (as EN store) and create new stores for Balrics (as RU store)
     * and for Russian group (as RU store).
     */
    private function processStores()
    {
        /* load default store and update it as Baltic_EN*/
        $this->storeBalticEn = $this->saveStore(
            self::DEF_STORE_ID_DEFAULT, true, 'EN' /* don't change 'default' code for default store */
        );
        /* create RU-store for Baltic group */
        $this->storeBalticRu = $this->saveStore(
            self::DEF_STORE_ID_BALTIC_RU, true, 'RU', 'baltic_ru',
            Cfg::DEF_WEBSITE_ID_BASE, $this->groupBaltic->getId(), 10
        );
        /* create Ru-store for Russian group */
        $this->storeRussianRu = $this->saveStore(
            self::DEF_STORE_ID_RUSSIAN_RU, true, 'RU', 'russian_ru',
            Cfg::DEF_WEBSITE_ID_BASE, $this->groupRussian->getId(), 10
        );
    }

    /**
     * Setup store related taxes configuration.
     */
    private function processTaxes()
    {
        /* Store / Configuration */
        $this->saveCfgForRussian('general/country/default', 'RU');
        $this->saveCfgForRussian('general/locale/code', 'ru_RU');
        $this->saveCfgForRussian('currency/options/default', 'RUB');
        $this->saveCfgForRussian('tax/defaults/country', 'RU');
        /* Tax Rates */
        $rateIdLv = $this->saveTaxRate('LV', 'LV Tax', 21);
        $rateIdRu = $this->saveTaxRate('RU', 'RU Tax', 18);
        /* Tax Rules */
        $ruleIdLv = $this->saveTaxRule('LV Tax');
        $ruleIdRu = $this->saveTaxRule('RU Tax');
        /* Tax calcs */
        $this->saveTaxCalc($rateIdLv, $ruleIdLv);
        $this->saveTaxCalc($rateIdRu, $ruleIdRu);
    }

    private function saveCfgForRussian($path, $value)
    {
        $entity = Cfg::ENTITY_MAGE_CORE_CONFIG_DATA;
        $stoerViewId = $this->storeRussianRu->getId();
        $bind = [
            Cfg::E_CONFIG_A_SCOPE => Cfg::SCOPE_CFG_STORES,
            Cfg::E_CONFIG_A_SCOPE_ID => $stoerViewId,
            Cfg::E_CONFIG_A_PATH => $path,
            Cfg::E_CONFIG_A_VALUE => $value
        ];
        $this->daoGeneric->replaceEntity($entity, $bind);
    }

    /**
     * Update/create store (frontname: "Store View").
     *
     * @param int $storeId ID to update existing store or 'null' to create new one
     * @param string $name
     * @param string $code
     * @param int $websiteId
     * @param int $groupId (frontname: "Store")
     * @param int $sortOrder
     * @param bool $isActive
     * @return \Magento\Store\Model\Store
     */
    private function saveStore(
        $storeId = null,
        $isActive = false,
        $name = null,
        $code = null,
        $websiteId = null,
        $groupId = null,
        $sortOrder = null
    ) {
        $event = 'store_add';
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->manObj->create(\Magento\Store\Model\Store::class);
        $store->load($storeId);
        /* 'code' is required attr. and should be set for existing store */
        $event = is_null($store->getCode()) ? 'store_add' : 'store_edit';
        $store->setIsActive($isActive);
        if (!is_null($name)) $store->setName($name);
        if (!is_null($code)) $store->setCode($code);
        if (!is_null($websiteId)) $store->setWebsiteId($websiteId);
        if (!is_null($websiteId)) $store->setGroupId($groupId);
        if (!is_null($sortOrder)) $store->setSortOrder($sortOrder);
        $store->save();
        /** @var  \Magento\Store\Model\StoreManager */
        $this->manStore->reinitStores();
        /** @var  \Magento\Framework\Event\ManagerInterface */
        $this->manEvent->dispatch($event, ['store' => $store]);
        return $store;
    }

    private function saveTaxCalc($rateId, $ruleId)
    {
        $found = $this->getTaxCalcs($rateId, $ruleId);
        if (!$found) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC;
            $bind = [
                Cfg::E_TAX_CALC_A_RATE_ID => $rateId,
                Cfg::E_TAX_CALC_A_RULE_ID => $ruleId,
                Cfg::E_TAX_CALC_A_CUST_TAX_CLASS_ID => 3,
                Cfg::E_TAX_CALC_A_PROD_TAX_CLASS_ID => 2
            ];
            $this->daoGeneric->replaceEntity($entity, $bind);
        }
    }

    private function saveTaxRate($country, $code, $rate)
    {
        $result = $this->getTaxRateByCode($code);
        if (!$result) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC_RATE;
            $bind = [
                EntityTaxRate::KEY_COUNTRY_ID => $country,
                EntityTaxRate::KEY_CODE => $code,
                EntityTaxRate::KEY_PERCENTAGE_RATE => $rate,
                EntityTaxRate::KEY_POSTCODE => '*'
            ];
            $result = $this->daoGeneric->addEntity($entity, $bind);
        }
        return $result;
    }

    private function saveTaxRule($code)
    {
        $result = $this->getTaxRuleByCode($code);
        if (!$result) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC_RULE;
            $bind = [
                EntityTaxRate::KEY_CODE => $code
            ];
            $result = $this->daoGeneric->addEntity($entity, $bind);
        }
        return $result;
    }
}