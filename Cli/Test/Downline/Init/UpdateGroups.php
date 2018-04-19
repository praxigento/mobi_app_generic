<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init;

/**
 * Update customer groups according to Santegra project.
 */
class UpdateGroups
{
    /** @var \Praxigento\App\Generic2\Helper\Odoo\BusinessCodesManager */
    protected $manBusCodes;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    protected $daoGeneric;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $daoGroup;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\App\Generic2\Helper\Odoo\BusinessCodesManager $manBusCodes,
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric,
        \Magento\Customer\Api\GroupRepositoryInterface $daoGroup

    ) {
        $this->manObj = $manObj;
        $this->resource = $resource;
        $this->manBusCodes = $manBusCodes;
        $this->daoGeneric = $daoGeneric;
        $this->daoGroup = $daoGroup;
    }

    public function do()
    {
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $all = $this->daoGroup->getList($crit);
        /** @var \Magento\Customer\Model\Data\Group $item */
        foreach ($all->getItems() as $item) {
            $groupId = $item->getId();
            $codeSaved = $item->getCode();
            if ($codeSaved == 'NOT LOGGED IN') continue; // don't change hardocded values
            $codeExpected = $this->manBusCodes->getBusCodeForCustomerGroupById($groupId);
            if ($codeExpected != $codeSaved) {
                $item->setCode($codeExpected);
                $this->daoGroup->save($item);
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
            $this->daoGroup->save($group);
        }

    }
}