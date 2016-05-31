<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Init_Test extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoAggWrhs;
    /** @var  Init */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoAggWrhs = $this->_mock(\Praxigento\Odoo\Repo\Agg\IWarehouse::class);
        /** create object to test */
        $this->obj = new Init(
            $this->mRepoAggWrhs
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Init::class, $this->obj);
    }

    public function test_warehouse()
    {
        /** === Setup Mocks === */
        $this->mRepoAggWrhs
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->warehouse();
    }
}