<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2;
include_once(__DIR__ . '/phpunit_bootstrap.php');

class Config_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_constructor() {
        $cfg = new Config();
        $this->assertInstanceOf('Praxigento\App\Generic2\Config', $cfg);
    }

}