<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Init\Sub;

use Magento\Framework\App\ObjectManager;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SalesRules_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Praxigento\App\Generic2\Cli\Init\Sub\SalesRules */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\App\Generic2\Cli\Init\Sub\SalesRules::class);
    }

    public function test_init()
    {
        $this->_obj->init();
        return;
    }

}