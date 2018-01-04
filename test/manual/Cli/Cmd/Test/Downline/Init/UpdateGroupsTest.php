<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

use Magento\Framework\App\ObjectManager;


class UpdateGroupsManualTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Praxigento\App\Generic2\Cli\Test\Downline\Init\UpdateGroups */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = ObjectManager::getInstance()->create(\Praxigento\App\Generic2\Cli\Test\Downline\Init\UpdateGroups::class);
    }

    public function test_do()
    {
        $this->obj->do();
        return;
    }

}