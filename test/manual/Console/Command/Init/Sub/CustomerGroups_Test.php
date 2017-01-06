<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

use Magento\Framework\App\ObjectManager;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class CustomerGroups_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->obj = ObjectManager::getInstance()->create(\Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups::class);
    }

    public function test_updateGroups()
    {
        $this->obj->updateGroups();
        return;
    }

}