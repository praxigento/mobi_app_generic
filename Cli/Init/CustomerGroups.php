<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Initialize customer groups for Generic project.
 */
class CustomerGroups
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** Total number jf the customer groups */
    const TOTAL_GROUPS = 5;

    /** @var \Praxigento\Odoo\Tool\IBusinessCodesManager */
    protected $manBusCodes;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $daoGroup;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $manBusCodes,
        \Magento\Customer\Api\GroupRepositoryInterface $daoGroup
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init:groups',
            'Initialize customer groups for Generic project.'
        );
        $this->manBusCodes = $manBusCodes;
        $this->daoGroup = $daoGroup;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->checkAreaCode();
        $this->processGroups();
        $output->writeln('<info>Command is completed.<info>');
    }

    protected function processGroups()
    {
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        $all = $this->daoGroup->getList($crit);
        /** @var \Magento\Customer\Model\Data\Group $item */
        foreach ($all->getItems() as $item) {
            $groupId = $item->getId();
            $codeSaved = $item->getCode();
            if ($codeSaved == Cfg::DEF_CUST_GROUP_ANON_CODE) continue; // don't change 'NOT LOGGED IN' code
            $codeExpected = $this->manBusCodes->getBusCodeForCustomerGroupById($groupId);
            if ($codeExpected != $codeSaved) {
                $item->setCode($codeExpected);
                $this->daoGroup->save($item);
            }
        }
        /* create additional groups (with id>=4, where '4'  - is count of the default groups in fresh M2 installation) */
        $total = $all->getTotalCount();
        $required = self::TOTAL_GROUPS - 1;
        if ($total <= $required) {
            $taxId = $item->getTaxClassId(); // get data from last item
            $taxName = $item->getTaxClassName(); // get data from last item
            $groupId = $total;
            while ($groupId <= $required) {
                /** @var \Magento\Customer\Model\Data\Group $group */
                $group = $this->manObj->create(\Magento\Customer\Model\Data\Group::class);
                $code = $this->manBusCodes->getBusCodeForCustomerGroupById($groupId);
                $groupId++;
                $group->setCode($code);
                $group->setTaxClassId($taxId);
                $group->setTaxClassName($taxName);
                $this->daoGroup->save($group);
            }
        }
    }
}