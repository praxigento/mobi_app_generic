<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

/**
 * For tests only.
 */
class CustomersChild extends Customers
{

    public function launchExecute($input, $output)
    {
        $this->execute($input, $output);
    }
}

class Customers_Test extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mInput;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mOutput;
    /** @var  \Mockery\MockInterface */
    private $mRepoMageCustomer;
    /** @var  \Mockery\MockInterface */
    private $mToolReferal;
    /** @var  CustomersChild */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoMageCustomer = $this->_mock(\Magento\Customer\Model\ResourceModel\CustomerRepository::class);
        $this->mToolReferal = $this->_mock(\Praxigento\Downline\Tool\IReferral::class);
        /* parameters */
        $this->mInput = $this->_mock(\Symfony\Component\Console\Input\InputInterface::class);
        $this->mOutput = $this->_mock(\Symfony\Component\Console\Output\OutputInterface::class);
        /** create object to test */
        $this->obj = new CustomersChild(
            $this->mManObj,
            $this->mManTrans,
            $this->mRepoMageCustomer,
            $this->mToolReferal
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Customers::class, $this->obj);
    }


    public function test_execute()
    {
        /** === Test Data === */
        $IDS = '1,2,3';
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
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_toolReferral->replaceCodeInRegistry($referralCode);
        $this->mToolReferal
            ->shouldReceive('replaceCodeInRegistry');
        // $customer = $this->_manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
        $mCustomer = $this->_mock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $this->mManObj
            ->shouldReceive('create')
            ->with(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->andReturn($mCustomer);
        // $customer->setEmail($email);
        // $customer->setFirstname($first);
        // $customer->setLastname($last);
        $mCustomer->shouldReceive('setEmail', 'setFirstname', 'setLastname');
        // $saved = $this->_repoMageCustomer->save($customer, $this->DEFAULT_PASSWORD_HASH);
        $mSaved = $this->_mock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $this->mRepoMageCustomer
            ->shouldReceive('save')
            ->andReturn($mSaved);
        // .. $saved->getId() ...
        $mSaved->shouldReceive('getId');
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