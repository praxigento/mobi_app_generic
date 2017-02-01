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
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $repoGeneric;

    public function __construct(
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        $this->repoGeneric = $repoGeneric;
    }

    public function do()
    {
        $this->repoGeneric->deleteEntity(\Praxigento\Downline\Data\Entity\Change::ENTITY_NAME);
        $this->repoGeneric->deleteEntity(\Praxigento\Downline\Data\Entity\Customer::ENTITY_NAME);
        $this->repoGeneric->deleteEntity(Cfg::ENTITY_MAGE_CUSTOMER);

    }
}