<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Tool\Warehouse\Def;

use \Praxigento\App\Generic2\Console\Command\Init\Stocks as InitStocks;

class StockManager implements \Praxigento\Warehouse\Tool\IStockManager
{
    /**
     * Expected values for stores & stocks IDs.
     */
    const DEF_STOCK_ID_BALTIC = InitStocks::DEF_STOCK_ID_BALTIC;
    const DEF_STOCK_ID_RUSSIAN = InitStocks::DEF_STOCK_ID_RUSSIAN;
    const DEF_GROUP_ID_BALTIC = InitStocks::DEF_GROUP_ID_BALTIC;
    const DEF_GROUP_ID_RUSSIAN = InitStocks::DEF_GROUP_ID_RUSSIAN;

    /** @var  \Magento\Store\Model\StoreManagerInterface */
    protected $_manStore;
    protected $_mapGroupToStock = [
        self::DEF_GROUP_ID_BALTIC => self::DEF_STOCK_ID_BALTIC,
        self::DEF_GROUP_ID_RUSSIAN => self::DEF_STOCK_ID_RUSSIAN
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
}