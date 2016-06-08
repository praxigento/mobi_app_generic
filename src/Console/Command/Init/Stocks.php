<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stocks extends Command
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

    /** @var  \Magento\Store\Model\Group */
    protected $_groupBaltic;
    /** @var  \Magento\Store\Model\Group */
    protected $_groupRussian;
    /** @var  \Magento\Store\Api\GroupRepositoryInterface */
    protected $_mageRepoGroup;
    /** @var  \Magento\CatalogInventory\Api\StockRepositoryInterface */
    protected $_mageRepoStock;
    /** @var  \Magento\Store\Api\StoreRepositoryInterface */
    protected $_mageRepoStore;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var  \Praxigento\Odoo\Repo\Agg\IWarehouse */
    protected $_repoWrhs;
    /** @var \Magento\Store\Model\Store */
    protected $_storeBalticEn;
    /** @var \Magento\Store\Model\Store */
    protected $_storeBalticRu;
    /** @var \Magento\Store\Model\Store */
    protected $_storeRussianRu;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Store\Api\GroupRepositoryInterface $mageRepoGroup,
        \Magento\Store\Api\StoreRepositoryInterface $mageRepoStore,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $mageRepoStock,
        \Praxigento\Odoo\Repo\Agg\IWarehouse $repoWrhs
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_mageRepoGroup = $mageRepoGroup;
        $this->_mageRepoStore = $mageRepoStore;
        $this->_mageRepoStock = $mageRepoStock;
        $this->_repoWrhs = $repoWrhs;
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
        $stockBaltic->setWebsiteId(self::DEF_WEBSITE_ID_MAIN);
        $this->_mageRepoStock->save($stockBaltic);
        /* create new stock as Russian */
        try {
            $stockRussian = $this->_mageRepoStock->get(self::DEF_STOCK_ID_RUSSIAN);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $stockRussian = $this->_manObj->create(\Magento\CatalogInventory\Model\Stock::class);
            // $stockRussian->setStockId(self::DEF_STOCK_ID_RUSSIAN);
        } finally {
            $stockRussian->setStockName('Russian');
            $stockRussian->setWebsiteId(self::DEF_WEBSITE_ID_MAIN);
            $this->_mageRepoStock->save($stockRussian);
        }
        /* create new warehouses */
        /* Baltic */
        $wrhsBaltic = $this->_repoWrhs->getById(self::DEF_STOCK_ID_BALTIC);
        $wrhsBaltic->setCode('Baltic');
        $wrhsBaltic->setCurrency('USD');
        $wrhsBaltic->setNote('Warehouse for Baltic states (LV, LT, EE)');
        $wrhsBaltic->setOdooId(22);
        $this->_repoWrhs->updateById($wrhsBaltic->getId(), $wrhsBaltic);
        /* Russian */
        $wrhsRussian = $this->_repoWrhs->getById(self::DEF_STOCK_ID_RUSSIAN);
        $shoudCreate = is_null($wrhsRussian->getOdooId());
        $wrhsRussian->setCode('Russian');
        $wrhsRussian->setCurrency('USD');
        $wrhsRussian->setNote('Warehouse for Russian Federation');
        $wrhsRussian->setOdooId(33);
        if ($shoudCreate) {
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
        $trans = $this->_manTrans->transactionBegin();
        try {
            $this->_processGroups();
            $this->_processStores();
            $this->_processStocks();
            $this->_manTrans->transactionCommit($trans);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
    }
}