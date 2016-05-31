<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;


class Init
{
    /** @var \Praxigento\Odoo\Repo\Agg\IWarehouse */
    protected $_repoAggWarehouse;

    public function __construct(
        \Praxigento\Odoo\Repo\Agg\IWarehouse $repoAggWarehouse
    ) {
        $this->_repoAggWarehouse = $repoAggWarehouse;
    }

    public function warehouse()
    {
        /**
         * TODO: Warehouse initialization should be overridden in app specific code.
         */
        $data = new \Praxigento\Odoo\Data\Agg\Warehouse();
        $data->setId(1);
        $data->setWebsiteId(0);
        $data->setCode('DEFAULT');
        $data->setOdooId(21); // see ../data.json
        $data->setCurrency('USD');
        $data->setNote('Default warehouse.');
        $this->_repoAggWarehouse->create($data);
    }
}