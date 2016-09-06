<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Praxigento\App\Generic2\Tool\Odoo\Def\BusinessCodesManager;

class Customers
    extends \Praxigento\App\Generic2\Console\Command\Init\Base
{
    /** Default downline tree */
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
    /** Retail customers */
    protected $GROUP_RETAIL = [9, 12];
    /** @var string 'UserPassword12 */
    protected $DEFAULT_PASSWORD_HASH = '387cf1ea04874290e8e3c92836e1c4b630c5abea110d8766bddb4b3a6224ea04:QVIfkMF7kfwRkkC3HdqJ84K1XANG38LF:1';
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
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
    /** @var \Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups */
    protected $_subCustomerGroups;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $repoMageCustomer,
        \Praxigento\Downline\Tool\IReferral $toolReferral,
        \Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups $subCustomerGroups
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init-customers',
            'Create sample downline tree in application.'
        );
        $this->_manTrans = $manTrans;
        $this->_repoMageCustomer = $repoMageCustomer;
        $this->_toolReferral = $toolReferral;
        $this->_subCustomerGroups = $subCustomerGroups;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->_manTrans->begin();
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
                /* MOBI-427: change group ID for retail customers */
                if (in_array($custId, $this->GROUP_RETAIL)) {
                    $customer->setGroupId(BusinessCodesManager::M_CUST_GROUP_RETAIL);
                }
                /** @var \Magento\Customer\Api\Data\CustomerInterface $saved */
                $saved = $this->_repoMageCustomer->save($customer, $this->DEFAULT_PASSWORD_HASH);
                $this->_mapCustomerMageIdByIndex[$custId] = $saved->getId();
                $this->_mapCustomerIndexByMageId[$saved->getId()] = $custId;
            }
            /* MOBI-426 : rename customer groups according to Generic App scheme. */
            $this->_subCustomerGroups->renameGroups();
            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }

}