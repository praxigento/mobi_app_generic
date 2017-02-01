<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

use Praxigento\App\Generic2\Tool\Odoo\Def\BusinessCodesManager;

class CustomerGroups
    extends \Praxigento\Core\Console\Command\Base
{
    /** @var \Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups */
    protected $subCustomerGroups;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCustomer,
        \Praxigento\Downline\Tool\IReferral $toolReferral,
        \Praxigento\App\Generic2\Console\Command\Init\Sub\CustomerGroups $subCustomerGroups
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init-customers',
            'Create sample downline tree in application.'
        );
        $this->manTrans = $manTrans;
        $this->repoCustomer = $repoCustomer;
        $this->toolReferral = $toolReferral;
        $this->subCustomerGroups = $subCustomerGroups;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->manTrans->begin();
        try {
            foreach ($this->DEFAULT_DWNL_TREE as $custId => $parentId) {
                $first = 'User' . $custId;
                $last = 'Last';
                $email = "customer_$custId@test.com";
                if ($custId != $parentId) {
                    /* save parent ID to registry */
                    $referralCode = $this->mapCustomerMageIdByIndex[$parentId];
                    $this->toolReferral->replaceCodeInRegistry($referralCode);
                }
                /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
                $customer = $this->manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class);
                $customer->setEmail($email);
                $customer->setFirstname($first);
                $customer->setLastname($last);
                /* MOBI-427: change group ID for retail customers */
                if (in_array($custId, $this->GROUP_RETAIL)) {
                    $customer->setGroupId(BusinessCodesManager::M_CUST_GROUP_RETAIL);
                }
                if (in_array($custId, $this->GROUP_WHOLESALE)) {
                    $customer->setGroupId(BusinessCodesManager::M_CUST_GROUP_WHOLESALE);
                }
                /** @var \Magento\Customer\Api\Data\CustomerInterface $saved */
                $saved = $this->repoCustomer->save($customer, $this->DEFAULT_PASSWORD_HASH);
                $this->mapCustomerMageIdByIndex[$custId] = $saved->getId();
                $this->mapCustomerIndexByMageId[$saved->getId()] = $custId;
            }
            /* MOBI-426 : rename customer groups according to Generic App scheme and create new ones. */
            $this->subCustomerGroups->updateGroups();
            $this->manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->manTrans->end($def);
        }
    }

}