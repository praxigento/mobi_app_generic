<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2;
include_once(__DIR__ . '/phpunit_bootstrap.php');

class Config_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_lib() {
        $ctx = \Praxigento\Core\Lib\Context::instance();
        $this->assertTrue($ctx instanceof \Praxigento\Core\Lib\Context);
    }

}