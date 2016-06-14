<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Tool\Warehouse\Def;

use \Praxigento\App\Generic2\Console\Command\Init\Stocks as Init;

class StockManager implements \Praxigento\Warehouse\Tool\IStockManager
{

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $_manStore;
    protected $_mapGroupToStock = [
        Init::DEF_GROUP_ID_BALTIC => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_GROUP_ID_RUSSIAN => Init::DEF_STOCK_ID_RUSSIAN
    ];
    protected $_mapStoreToStock = [
        Init::DEF_STORE_ID_BALTIC_EN => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_STORE_ID_BALTIC_RU => Init::DEF_STOCK_ID_BALTIC,
        Init::DEF_STORE_ID_RUSSIAN_RU => Init::DEF_STOCK_ID_RUSSIAN
    ];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_manStore = $storeManager;
    }

    public function getCurrentStockId()
    {
        $group = $this->_manStore->getGroup();
        $id = $group->getId();
        $result = isset($this->_mapGroupToStock[$id]) ?
            $this->_mapGroupToStock[$id] :
            \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
        return $result;
    }

    public function getStockIdByStoreId($storeId)
    {
        $result = isset($this->_mapStoreToStock[$storeId]) ?
            $this->_mapStoreToStock[$storeId] :
            \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
        return $result;
    }
}