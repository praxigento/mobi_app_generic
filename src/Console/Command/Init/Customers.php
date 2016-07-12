<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Customers 
    extends \Symfony\Component\Console\Command\Command
{
    /**
     * Downline tree default dependencies.
     * @var array
     */
    protected $DEFAULT_DWNL_TREE = [
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 2,
        5 => 2,
        6 => 3,
        7 => 3,
        8 => 6,
        9 => 6,
        10 => 7,
        11 => 7,
        12 => 10,
        13 => 10
    ];
    /** @var string 'UserPassword12 */
    protected $DEFAULT_PASSWORD_HASH = '387cf1ea04874290e8e3c92836e1c4b630c5abea110d8766bddb4b3a6224ea04:QVIfkMF7kfwRkkC3HdqJ84K1XANG38LF:1';
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /**
     * Map index by Magento ID (index started from 1).
     *
     * @var array [ $entityId  => $index, ... ]
     */
    protected $_mapCustomerIndexByMageId = [];
    /**
     * Map Magento ID by index (index started from 1).
     *
     * @var array [ $index  => $entityId, ... ]
     */
    protected $_mapCustomerMageIdByIndex = [];
    /** @var \Magento\Customer\Model\ResourceModel\CustomerRepository */
    protected $_repoMageCustomer;
    /** @var \Praxigento\Downline\Tool\IReferral */
    protected $_toolReferral;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $repoMageCustomer,
        \Praxigento\Downline\Tool\IReferral $toolReferral
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_repoMageCustomer = $repoMageCustomer;
        $this->_toolReferral = $toolReferral;
    }

    /**
     * Sets area code to start a session for replication.
     */
    private function _setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $config = $configLoader->load($areaCode);
        $this->_manObj->configure($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prxgt:app:init-customers');
        $this->setDescription('Create sample downline tree in application.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* setup session */
        $this->_setAreaCode();
        $trans = $this->_manTrans->transactionBegin();
        try {
            foreach ($this->DEFAULT_DWNL_TREE as $custId => $parentId) {
                $first = 'User' . $custId;
                $last = 'Last';
                $email = "customer_$custId@test.com";
                if ($custId != $parentId) {
                    /* save parent ID to registry */
                    $referralCode = $this->_mapCustomerMageIdByIndex[$parentId];
                    $this->_toolReferral->replaceCodeInRegistry($referralCode);
                }
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->_manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
                $customer->setEmail($email);
                $customer->setFirstname($first);
                $customer->setLastname($last);
                /** @var \Magento\Customer\Api\Data\CustomerInterface $saved */
                $saved = $this->_repoMageCustomer->save($customer, $this->DEFAULT_PASSWORD_HASH);
                $this->_mapCustomerMageIdByIndex[$custId] = $saved->getId();
                $this->_mapCustomerIndexByMageId[$saved->getId()] = $custId;
            }
            $this->_manTrans->transactionCommit($trans);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
    }

}