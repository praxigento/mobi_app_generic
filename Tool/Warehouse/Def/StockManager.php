<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Tool\Warehouse\Def;

use Praxigento\App\Generic2\Cli\Init\Stocks as Init;

class StockManager implements \Praxigento\Warehouse\Api\Helper\Stock
{
    /** @var \Praxigento\Warehouse\Repo\Dao\Warehouse */
    private $daoWrhs;
    /** @var  \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    private $mapGroupToStock = [
        Init::DEF_GROUP_ID_BALTIC => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_GROUP_ID_RUSSIAN => Init::DEF_STOCK_ID_RUSSIAN
    ];
    private $mapStoreToStock = [
        Init::DEF_STORE_ID_BALTIC_EN => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_STORE_ID_BALTIC_RU => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_STORE_ID_RUSSIAN_RU => Init::DEF_STOCK_ID_RUSSIAN
    ];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Praxigento\Warehouse\Repo\Dao\Warehouse $daoWrhs
    ) {
        $this->manStore = $storeManager;
        $this->daoWrhs = $daoWrhs;
    }

    public function getCurrentStockId()
    {
        $group = $this->manStore->getGroup();
        $id = $group->getId();
        $result = isset($this->mapGroupToStock[$id]) ?
            $this->mapGroupToStock[$id] :
            \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
        return $result;
    }

    public function getDefaultStockId()
    {
        return Init::DEF_STOCK_ID_BALTIC;
    }

    public function getStockCurrencyByStoreId($storeId)
    {
        $stockId = $this->getStockIdByStoreId($storeId);
        $wrhs = $this->daoWrhs->getById($stockId);
        $result = $wrhs->getCurrency();
        return $result;
    }

    public function getStockIdByStoreId($storeId)
    {
        $result = isset($this->mapStoreToStock[$storeId]) ?
            $this->mapStoreToStock[$storeId] :
            \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
        return $result;
    }
}