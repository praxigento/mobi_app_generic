<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Remove customer's test data.
 */
class CleanCustomers
{
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    protected $daoGeneric;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        $this->daoGeneric = $daoGeneric;
    }

    public function do()
    {
        $this->daoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Snap::ENTITY_NAME);
        $this->daoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Change::ENTITY_NAME);
        $this->daoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Customer::ENTITY_NAME);
        $this->daoGeneric->deleteEntity(Cfg::ENTITY_MAGE_CUSTOMER);

    }
}