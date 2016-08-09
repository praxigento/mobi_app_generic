<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

use Magento\Framework\App\ObjectManager;
use Praxigento\App\Generic2\Console\Command\Init\Stocks as InitStocks;

class SalesRules
{
    /** @var \Magento\Framework\Api\Search\SearchCriteriaBuilder */
    protected $_builderSearchCriteria;
    /** @var \Magento\Rule\Model\Condition\Context */
    protected $_contextCondition;
    /** @var \Magento\SalesRule\Model\Converter\ToDataModel */
    protected $_convertRule;
    protected $_custGroups = [];
    /** @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory */
    protected $_factoryCondCombain;
    /** @var \Magento\SalesRule\Api\Data\ConditionInterfaceFactory */
    protected $_factoryCondition;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $_repoCustomerGroup;
    /** @var \Magento\SalesRule\Api\RuleRepositoryInterface */
    protected $_repoRule;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $builderSearchCriteria,
        \Magento\SalesRule\Model\Converter\ToDataModel $convertRule,
        \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $factoryCondition,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $factoryCondCombain,
        \Magento\Customer\Api\GroupRepositoryInterface $repoCustomerGroup,
        \Magento\SalesRule\Api\RuleRepositoryInterface $repoRule,
        \Magento\Rule\Model\Condition\Context $contextCondition

    ) {
        $this->_builderSearchCriteria = $builderSearchCriteria;
        $this->_convertRule = $convertRule;
        $this->_factoryCondition = $factoryCondition;
        $this->_factoryCondCombain = $factoryCondCombain;
        $this->_repoCustomerGroup = $repoCustomerGroup;
        $this->_repoRule = $repoRule;
        $this->_contextCondition = $contextCondition;
    }

    private function _createRuleBySku(
        $name,
        $description,
        $sku,
        $discountPercent,
        $applyToShipping = false

    ) {
        /* rule */
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule::class);
        $rule->setName($name);
        $rule->setDescription($description);
        $rule->setIsActive(true);
        $rule->setWebsiteIds([InitStocks::DEF_WEBSITE_ID_MAIN]);
        $rule->setCustomerGroupIds($this->_custGroups);
        $rule->setDiscountAmount($discountPercent);
        $rule->setStopRulesProcessing(false);
        $rule->setApplyToShipping($applyToShipping);
        $rule->setSimpleAction(\Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION);
        /* combined condition */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Combine $combo */
        $combo = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Combine::class);
        $rule->setConditions($combo);
        /* found products */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Product\Found $found */
        $found = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Product\Found::class);
        $combo->setConditions([$found]);
        /* condition */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Product $cond */
        $cond = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Product::class);
        $found->setConditions([$cond]);
        $cond->setType(\Magento\SalesRule\Model\Rule\Condition\Product::class);
        $cond->setAttribute('sku');
        $cond->setOperator('==');
        $cond->setValue($sku);
        /* action */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Combine $act */
        $act = $combo = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Combine::class);
        $rule->setActions($act);
        /** @var \Magento\SalesRule\Model\Rule\Condition\Product $cond */
        $actCond = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Product::class);
        $act->setConditions([$actCond]);
        $actCond->setType(\Magento\SalesRule\Model\Rule\Condition\Product::class);
        $actCond->setAttribute('sku');
        $actCond->setOperator('==');
        $actCond->setValue($sku);
        /* save rule */
        $rule->save();
    }

    private function _createRuleForOrder(
        $name,
        $description,
        $levelAmount,
        $discountAmount

    ) {
        /* rule */
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule::class);
        $rule->setName($name);
        $rule->setDescription($description);
        $rule->setIsActive(true);
        $rule->setWebsiteIds([InitStocks::DEF_WEBSITE_ID_MAIN]);
        $rule->setCustomerGroupIds($this->_custGroups);
        $rule->setDiscountAmount($discountAmount);
        $rule->setStopRulesProcessing(false);
        $rule->setSimpleAction(\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION);
        /* combined condition */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Combine $combo */
        $combo = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Combine::class);
        $rule->setConditions($combo);
        /* condition */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Address $cond */
        $cond = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Address::class);
        $combo->setConditions([$cond]);
        $cond->setType(\Magento\SalesRule\Model\Rule\Condition\Address::class);
        $cond->setAttribute('base_subtotal');
        $cond->setOperator('>=');
        $cond->setValue($levelAmount);
        /* action */
        /** @var \Magento\SalesRule\Model\Rule\Condition\Combine $act */
        $act = $combo = ObjectManager::getInstance()->create(\Magento\SalesRule\Model\Rule\Condition\Combine::class);
        $rule->setActions($act);
        /* save rule */
        $rule->save();
    }

    private function _loadCustomerGroups()
    {
        $crit = $this->_builderSearchCriteria->create();
        $crit->setSortOrders(null);
        $all = $this->_repoCustomerGroup->getList($crit);
        /** @var \Magento\Customer\Model\Data\Group $item */
        foreach ($all->getItems() as $item) {
            $this->_custGroups[] = $item->getId();
        }
    }

    /**
     * Add sales rules.
     */
    public function init()
    {
        $crit = $this->_builderSearchCriteria->create();
        $crit->setSortOrders(null);
        /** @var \Magento\Framework\Api\SearchResults $all */
        $all = $this->_repoRule->getList($crit);
        $total = $all->getTotalCount();
        if ($total) {
            /** @var \Magento\SalesRule\Model\Data\Rule $item */
            foreach ($all->getItems() as $item) {
                $this->_repoRule->deleteById($item->getRuleId());
            }
        }
        $this->_loadCustomerGroups();
        $desc = 'This rule is added by initialization script for test proposes.';
        $this->_createRuleBySku('10% off to Bee Royal', $desc, '212San', 10);
        $this->_createRuleBySku('20% off to BoostIron', $desc, '10674San', 20);
        $this->_createRuleForOrder('$20 off to cart subtotal above $100', $desc, 100, 20);
    }
}