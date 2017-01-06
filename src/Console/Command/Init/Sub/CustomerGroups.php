<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

class CustomerGroups
{
    /** @var \Praxigento\App\Generic2\Tool\Odoo\Def\BusinessCodesManager */
    protected $manBusCodes;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $repoGeneric;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $repoGroup;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\App\Generic2\Tool\Odoo\Def\BusinessCodesManager $manBusCodes,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Magento\Customer\Api\GroupRepositoryInterface $repoGroup
    ) {
        $this->manObj = $manObj;
        $this->manBusCodes = $manBusCodes;
        $this->repoGeneric = $repoGeneric;
        $this->repoGroup = $repoGroup;
    }

    /**
     * Get and rename all existing customer groups and create new group for 'referra' if missed.
     */
    public function updateGroups()
    {
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $all = $this->repoGroup->getList($crit);
        /** @var \Magento\Customer\Model\Data\Group $item */
        foreach ($all->getItems() as $item) {
            $groupId = $item->getId();
            $codeSaved = $item->getCode();
            if ($codeSaved== 'NOT LOGGED IN') continue;
            $codeExpected = $this->manBusCodes->getBusCodeForCustomerGroupById($groupId);
            if ($codeExpected != $codeSaved) {
                $item->setCode($codeExpected);
                $this->repoGroup->save($item);
            }
        }
        /* create additional groups (id>=4) */
        $total = $all->getTotalCount();
        if ($total <= 4) {
            /* id=4: referral */
            /** @var \Magento\Customer\Model\Data\Group $group */
            $group = $this->manObj->create(\Magento\Customer\Model\Data\Group::class);
            $groupId = 4; // expected ID for referrals
            $code = $this->manBusCodes->getBusCodeForCustomerGroupById($groupId);
            $taxId = $item->getTaxClassId(); // get data from last item
            $taxName = $item->getTaxClassName(); // get data from last item
            $group->setCode($code);
            $group->setTaxClassId($taxId);
            $group->setTaxClassName($taxName);
            $this->repoGroup->save($group);
        }

    }
}