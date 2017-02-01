<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Test\Downline\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Remove customer's test data.
 */
class CleanCustomers
{
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $repoGeneric;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        $this->resource = $resource;
        $this->repoGeneric = $repoGeneric;
    }

    public function do()
    {
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $this->repoGeneric->deleteEntity(Cfg::ENTITY_MAGE_CUSTOMER);

    }
}