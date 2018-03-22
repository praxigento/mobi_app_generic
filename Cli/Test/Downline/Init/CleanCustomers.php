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
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    protected $repoGeneric;

    public function __construct(
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        $this->repoGeneric = $repoGeneric;
    }

    public function do()
    {
        $this->repoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Snap::ENTITY_NAME);
        $this->repoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Change::ENTITY_NAME);
        $this->repoGeneric->deleteEntity(\Praxigento\Downline\Repo\Data\Customer::ENTITY_NAME);
        $this->repoGeneric->deleteEntity(Cfg::ENTITY_MAGE_CUSTOMER);

    }
}