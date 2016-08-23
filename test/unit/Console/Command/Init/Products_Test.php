<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

/**
 * For tests only.
 */
class ProductsChild extends Products
{

    public function launchExecute($input, $output)
    {
        $this->execute($input, $output);
    }
}

class Products_Test extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallReplicate;
    /** @var  \Mockery\MockInterface */
    private $mInput;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mOutput;
    /** @var  \Mockery\MockInterface */
    private $mServiceInputProcessor;
    /** @var  \Mockery\MockInterface */
    private $mSubCats;
    /** @var  ProductsChild */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mServiceInputProcessor = $this->_mock(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $this->mCallReplicate = $this->_mock(\Praxigento\Odoo\Service\IReplicate::class);
        $this->mSubCats = $this->_mock(Sub\Categories::class);
        /* parameters */
        $this->mInput = $this->_mock(\Symfony\Component\Console\Input\InputInterface::class);
        $this->mOutput = $this->_mock(\Symfony\Component\Console\Output\OutputInterface::class);
        /** create object to test */
        $this->obj = new ProductsChild(
            $this->mManObj,
            $this->mManTrans,
            $this->mServiceInputProcessor,
            $this->mCallReplicate,
            $this->mSubCats
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Products::class, $this->obj);
    }


    public function test_execute()
    {
        /** === Test Data === */
        $CONFIG = ['config'];
        /** === Setup Mocks === */
        // $this->_setAreaCode();
        // $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $mAppState = $this->_mock(\Magento\Framework\App\State::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mAppState);
        // $appState->setAreaCode($areaCode);
        $mAppState->shouldReceive('setAreaCode')->once();
        // $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $mConfigLoader = $this->_mock(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->mManObj
            ->shouldReceive('get')->once()
            ->andReturn($mConfigLoader);
        // $config = $configLoader->load($areaCode);
        $mConfigLoader->shouldReceive('load')->once()
            ->andReturn($CONFIG);
        // $this->_manObj->configure($config);
        $this->mManObj
            ->shouldReceive('configure')->once();
        // return
        //
        // $bundle = $this->_serviceInputProcessor->convertValue(...);
        $mBundle = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        $this->mServiceInputProcessor
            ->shouldReceive('convertValue')->once()
            ->andReturn($mBundle);
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_subInit->warehouse();
        $this->mSubCats
            ->shouldReceive('warehouse')->once();
        // $req = $this->_manObj->create(ProductSaveRequest::class);
        $mReq = $this->_mock(\Praxigento\Odoo\Service\Replicate\Request\ProductSave::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mReq);
        // $req->setProductBundle($bundle);
        $mReq->shouldReceive('setProductBundle');
        // $this->_callReplicate->productSave($req);
        $this->mCallReplicate
            ->shouldReceive('productSave')->once();
        // $this->_subCats->enableForAllStoreViews();
        $this->mSubCats
            ->shouldReceive('enableForAllStoreViews')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $this->obj->launchExecute($this->mInput, $this->mOutput);
    }
}