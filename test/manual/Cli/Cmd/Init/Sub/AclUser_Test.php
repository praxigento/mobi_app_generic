<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Cmd\Init\Sub;

use Magento\Framework\App\ObjectManager;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class AclUser_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Praxigento\App\Generic2\Cli\Cmd\Init\Sub\AclUser */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\App\Generic2\Cli\Cmd\Init\Sub\AclUser::class);
    }

    public function test_create()
    {
        $this->_obj->createAclUsers();
        return;
    }

}