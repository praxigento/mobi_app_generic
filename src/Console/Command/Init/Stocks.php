<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Tax\Model\Calculation\Rate as EntityTaxRate;
use Magento\Tax\Model\Calculation\Rule as EntityTaxRule;
use Praxigento\App\Generic2\Config as Cfg;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stocks
    extends \Symfony\Component\Console\Command\Command
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
     * IDs for stores (store views in adminhtml terms).
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
     * IDs for websites.
     */
    const DEF_WEBSITE_ID_ADMIN = 0;
    const DEF_WEBSITE_ID_MAIN = 1;
    /**#@-  */

    /**#@+
     * Odoo IDs for warehouses.
     */
    const DEF_WRHS_ODOO_ID_BALTIC = 2;
    const DEF_WRHS_ODOO_ID_RUSSIAN = 3;
    /**#@-  */

    /** @var  \Magento\Store\Model\Group */
    protected $_groupBaltic;
    /** @var  \Magento\Store\Model\Group */
    protected $_groupRussian;
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var  \Magento\Store\Api\GroupRepositoryInterface */
    protected $_mageRepoGroup;
    /** @var  \Magento\CatalogInventory\Api\StockRepositoryInterface */
    protected $_mageRepoStock;
    /** @var  \Magento\Store\Api\StoreRepositoryInterface */
    protected $_mageRepoStore;
    /** @var  \Magento\Framework\Event\ManagerInterface */
    protected $_manEvent;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Magento\Store\Model\StoreManager */
    protected $_manStore;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;
    /** @var  \Praxigento\Odoo\Repo\Agg\IWarehouse */
    protected $_repoWrhs;
    /** @var \Magento\Store\Model\Store */
    protected $_storeBalticEn;
    /** @var \Magento\Store\Model\Store */
    protected $_storeBalticRu;
    /** @var \Magento\Store\Model\Store */
    protected $_storeRussianRu;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Store\Model\StoreManager $manStore,
        \Magento\Framework\Event\ManagerInterface $manEvent,
        \Magento\Store\Api\GroupRepositoryInterface $mageRepoGroup,
        \Magento\Store\Api\StoreRepositoryInterface $mageRepoStore,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $mageRepoStock,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Odoo\Repo\Agg\IWarehouse $repoWrhs
    ) {
        parent::__construct();
        $this->_logger = $logger;
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_manStore = $manStore;
        $this->_manEvent = $manEvent;
        $this->_mageRepoGroup = $mageRepoGroup;
        $this->_mageRepoStore = $mageRepoStore;
        $this->_mageRepoStock = $mageRepoStock;
        $this->_repoGeneric = $repoGeneric;
        $this->_repoWrhs = $repoWrhs;
    }

    private function _getTaxRateByCode($code)
    {
        $result = null;
        $entity = Cfg::ENTITY_MAGE_TAX_CALC_RATE;
        $cols = [Cfg::E_TAX_CALC_RATE_A_ID];
        $where = EntityTaxRate::KEY_CODE . '=' . $this->_repoGeneric->getConnection()->quote($code);
        $rows = $this->_repoGeneric->getEntities($entity, $cols, $where);
        if (is_array($rows)) {
            $one = reset($rows);
            $result = $one[Cfg::E_TAX_CALC_RATE_A_ID];
        }
        return $result;
    }

    private function _getTaxRuleByCode($code)
    {
        $result = null;
        $entity = Cfg::ENTITY_MAGE_TAX_CALC_RULE;
        $cols = [Cfg::E_TAX_CALC_RULE_A_ID];
        $where = EntityTaxRule::KEY_CODE . '=' . $this->_repoGeneric->getConnection()->quote($code);
        $rows = $this->_repoGeneric->getEntities($entity, $cols, $where);
        if (is_array($rows)) {
            $one = reset($rows);
            $result = $one[Cfg::E_TAX_CALC_RULE_A_ID];
        }
        return $result;
    }

    private function _getTaxCalcs($rateId, $ruleId)
    {
        $entity = Cfg::ENTITY_MAGE_TAX_CALC;
        $where = Cfg::E_TAX_CALC_A_RATE_ID . '=' . (int)$rateId;
        $where .= ' AND ' . Cfg::E_TAX_CALC_A_RULE_ID . '=' . (int)$ruleId;
        $rows = $this->_repoGeneric->getEntities($entity, null, $where);
        $result = is_array($rows) && count($rows);
        return $result;
    }

    /**
     * We cannot create tables in the DB transaction.
     */
    private function _initStores()
    {
        /* MOBI-312 : init store view (create sequences tables ) */
        $this->_manEvent->dispatch('store_add', ['store' => $this->_storeBalticEn]);
        $this->_manEvent->dispatch('store_add', ['store' => $this->_storeBalticRu]);
        $this->_manEvent->dispatch('store_add', ['store' => $this->_storeRussianRu]);
        $this->_manStore->reinitStores();
    }

    /**
     * Update default group (store) as 'Baltic' and create new group for 'Russian'.
     */
    private function _processGroups()
    {
        /* load and update Baltic group (store)*/
        $this->_groupBaltic = $this->_manObj->create(\Magento\Store\Model\Group::class);
        $this->_groupBaltic->load(self::DEF_GROUP_ID_MAIN);
        $rootCatId = $this->_groupBaltic->getRootCategoryId();
        $this->_groupBaltic->setName('Baltic');
        $this->_groupBaltic->save();
        /* create Russian group (store) */
        $this->_groupRussian = $this->_manObj->create(\Magento\Store\Model\Group::class);
        $this->_groupRussian->load(self::DEF_GROUP_ID_RUSSIAN);
        $this->_groupRussian->setName('Russian');
        $this->_groupRussian->setWebsiteId(self::DEF_WEBSITE_ID_MAIN);
        $this->_groupRussian->setRootCategoryId($rootCatId);
        $this->_groupRussian->save();
    }

    /**
     * Update default stock (switch to website #1 from website #0), add new stock and 2 warehouses; bind warehouses
     * with stocks.
     */
    private function _processStocks()
    {
        /* update default stock as Baltic */
        /** @var \Magento\CatalogInventory\Model\Stock $stockBaltic */
        $stockBaltic = $this->_mageRepoStock->get(self::DEF_STOCK_ID_DEFAULT);
        $stockBaltic->setStockName('Baltic');
        $stockBaltic->setWebsiteId(self::DEF_WEBSITE_ID_ADMIN);
        $this->_mageRepoStock->save($stockBaltic);
        /* create new stock as Russian */
        try {
            $stockRussian = $this->_mageRepoStock->get(self::DEF_STOCK_ID_RUSSIAN);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $stockRussian = $this->_manObj->create(\Magento\CatalogInventory\Model\Stock::class);
            // $stockRussian->setStockId(self::DEF_STOCK_ID_RUSSIAN);
        } finally {
            $stockRussian->setStockName('Russian');
            $stockRussian->setWebsiteId(self::DEF_WEBSITE_ID_ADMIN);
            $this->_mageRepoStock->save($stockRussian);
        }
        /* create new warehouses */
        /* Baltic */
        $wrhsBaltic = $this->_repoWrhs->getById(self::DEF_STOCK_ID_BALTIC);
        $shoudCreate = is_null($wrhsBaltic->getOdooId());
        $wrhsBaltic->setCode('Baltic');
        $wrhsBaltic->setCurrency('USD');
        $wrhsBaltic->setCountryCode('LV');
        $wrhsBaltic->setNote('Warehouse for Baltic states (LV, LT, EE)');
        $wrhsBaltic->setOdooId(self::DEF_WRHS_ODOO_ID_BALTIC);
        if ($shoudCreate) {
            $this->_repoWrhs->create($wrhsBaltic);
        } else {
            $this->_repoWrhs->updateById($wrhsBaltic->getId(), $wrhsBaltic);
        }
        /* Russian */
        $wrhsRussian = $this->_repoWrhs->getById(self::DEF_STOCK_ID_RUSSIAN);
        if (!$wrhsRussian) {
            $wrhsRussian = new \Praxigento\Odoo\Data\Agg\Warehouse();
        }
        $shoudCreate = is_null($wrhsRussian->getOdooId());
        $wrhsRussian->setCode('Russian');
        $wrhsRussian->setCurrency('USD');
        $wrhsRussian->setCountryCode('RU');
        $wrhsRussian->setNote('Warehouse for Russian Federation');
        $wrhsRussian->setOdooId(self::DEF_WRHS_ODOO_ID_RUSSIAN);
        if ($shoudCreate) {
            $wrhsRussian->setId($stockRussian->getStockId());
            $this->_repoWrhs->create($wrhsRussian);
        } else {
            $this->_repoWrhs->updateById($wrhsRussian->getId(), $wrhsRussian);
        }
        return;

    }

    /**
     * Update default store (store view) for Baltic group (as EN store) and create new stores for Balrics (as RU store)
     * and for Russian group (as RU store).
     */
    private function _processStores()
    {
        /* load default store and update it as Baltic_EN*/
        $this->_storeBalticEn = $this->_manObj->create(\Magento\Store\Model\Store::class);
        $this->_storeBalticEn->load(self::DEF_STORE_ID_DEFAULT);
        $this->_storeBalticEn->setName('EN');
        $this->_storeBalticEn->setCode('baltic_en');
        $this->_storeBalticEn->save();
        /* create RU-store for Baltic group */
        $this->_storeBalticRu = $this->_manObj->create(\Magento\Store\Model\Store::class);
        $this->_storeBalticRu->load(self::DEF_STORE_ID_BALTIC_RU);
        $this->_storeBalticRu->setName('RU');
        $this->_storeBalticRu->setCode('baltic_ru');
        $this->_storeBalticRu->setWebsiteId(self::DEF_WEBSITE_ID_MAIN);
        $this->_storeBalticRu->setGroupId($this->_groupBaltic->getId());
        $this->_storeBalticRu->setSortOrder(10);
        $this->_storeBalticRu->setIsActive(true);
        $this->_storeBalticRu->save();
        /* create Ru-store for Russian group */
        $this->_storeRussianRu = $this->_manObj->create(\Magento\Store\Model\Store::class);
        $this->_storeRussianRu->load(self::DEF_STORE_ID_RUSSIAN_RU);
        $this->_storeRussianRu->setName('RU');
        $this->_storeRussianRu->setCode('russian_ru');
        $this->_storeRussianRu->setWebsiteId(self::DEF_WEBSITE_ID_MAIN);
        $this->_storeRussianRu->setGroupId($this->_groupRussian->getId());
        $this->_storeRussianRu->setSortOrder(10);
        $this->_storeRussianRu->setIsActive(true);
        $this->_storeRussianRu->save();
    }

    /**
     * Setup store related taxes configuration.
     */
    private function _processTaxes()
    {
        /* Store / Configuration */
        $this->_saveCfgForRussian('general/country/default', 'RU');
        $this->_saveCfgForRussian('general/locale/code', 'ru_RU');
        $this->_saveCfgForRussian('currency/options/default', 'RUB');
        $this->_saveCfgForRussian('tax/defaults/country', 'RU');
        /* Tax Rates */
        $rateIdLv = $this->_saveTaxRate('LV', 'LV Tax', 21);
        $rateIdRu = $this->_saveTaxRate('RU', 'RU Tax', 18);
        /* Tax Rules */
        $ruleIdLv = $this->_saveTaxRule('LV Tax');
        $ruleIdRu = $this->_saveTaxRule('RU Tax');
        /* Tax calcs */
        $this->_saveTaxCalc($rateIdLv, $ruleIdLv);
        $this->_saveTaxCalc($rateIdRu, $ruleIdRu);
    }

    private function _saveCfgForRussian($path, $value)
    {
        $entity = Cfg::ENTITY_MAGE_CORE_CONFIG_DATA;
        $stoerViewId = $this->_storeRussianRu->getId();
        $bind = [
            Cfg::E_CONFIG_A_SCOPE => Cfg::SCOPE_CFG_STORES,
            Cfg::E_CONFIG_A_SCOPE_ID => $stoerViewId,
            Cfg::E_CONFIG_A_PATH => $path,
            Cfg::E_CONFIG_A_VALUE => $value
        ];
        $this->_repoGeneric->replaceEntity($entity, $bind);
    }

    private function _saveTaxRate($country, $code, $rate)
    {
        $result = $this->_getTaxRateByCode($code);
        if (!$result) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC_RATE;
            $bind = [
                EntityTaxRate::KEY_COUNTRY_ID => $country,
                EntityTaxRate::KEY_CODE => $code,
                EntityTaxRate::KEY_PERCENTAGE_RATE => $rate,
                EntityTaxRate::KEY_POSTCODE => '*'
            ];
            $result = $this->_repoGeneric->addEntity($entity, $bind);
        }
        return $result;
    }

    private function _saveTaxCalc($rateId, $ruleId)
    {
        $found = $this->_getTaxCalcs($rateId, $ruleId);
        if (!$found) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC;
            $bind = [
                Cfg::E_TAX_CALC_A_RATE_ID => $rateId,
                Cfg::E_TAX_CALC_A_RULE_ID => $ruleId,
                Cfg::E_TAX_CALC_A_CUST_TAX_CLASS_ID => 3,
                Cfg::E_TAX_CALC_A_PROD_TAX_CLASS_ID => 2
            ];
            $this->_repoGeneric->replaceEntity($entity, $bind);
        }
    }

    private function _saveTaxRule($code)
    {
        $result = $this->_getTaxRuleByCode($code);
        if (!$result) {
            $entity = Cfg::ENTITY_MAGE_TAX_CALC_RULE;
            $bind = [
                EntityTaxRate::KEY_CODE => $code
            ];
            $result = $this->_repoGeneric->addEntity($entity, $bind);
        }
        return $result;
    }

    /**
     * Sets area code to start a session for replication.
     */
    private function _setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $config = $configLoader->load($areaCode);
        $this->_manObj->configure($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prxgt:app:init-stocks');
        $this->setDescription('Create sample stores in application and map warehouses/stocks to stores.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* setup session */
        $this->_setAreaCode();
        $def = $this->_manTrans->begin();
        try {
            $this->_processGroups();
            $this->_processStores();
            $this->_processStocks();
            $this->_processTaxes();
            $this->_manTrans->commit($def);
            /* init stores w/o transaction (DDL is denied in the transaction )*/
            $this->_initStores();
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }
}