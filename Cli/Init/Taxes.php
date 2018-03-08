<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Cli\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Setup sale taxes for Generic project (prices excl. taxes).
 */
class Taxes
    extends \Praxigento\Core\App\Cli\Cmd\Base
{

    /**
     * Default Magento values for newly initialized DB.
     */
    const DEF_CUST_TAX_CLASS_ID = 3;
    const DEF_PROD_TAX_CLASS_ID = 2;
    /**
     * Fixed IDs for own data.
     */
    const DEF_TAX_RATE_ID_LV = 1;
    const DEF_TAX_RATE_ID_RU12 = 3;
    const DEF_TAX_RATE_ID_RU6 = 2;
    const DEF_TAX_RULE_ID_LV = 1;
    const DEF_TAX_RULE_ID_RU1 = 2;
    const DEF_TAX_RULE_ID_RU2 = 3;
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    protected $manTrans;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    protected $repoGeneric;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    )
    {
        parent::__construct(
            $manObj,
            'prxgt:app:init:taxes',
            'Setup sale taxes for Generic project (prices excl. taxes).'
        );
        $this->manTrans = $manTrans;
        $this->repoGeneric = $repoGeneric;
    }

    /**
     * Add links between rules & rates.
     */
    protected function addLinks()
    {
        /* link Rate LV with Rule LV */
        $data = [
            Cfg::E_TAX_CALC_A_RATE_ID => self::DEF_TAX_RATE_ID_LV,
            Cfg::E_TAX_CALC_A_RULE_ID => self::DEF_TAX_RULE_ID_LV,
            Cfg::E_TAX_CALC_A_CUST_TAX_CLASS_ID => self::DEF_CUST_TAX_CLASS_ID,
            Cfg::E_TAX_CALC_A_PROD_TAX_CLASS_ID => self::DEF_PROD_TAX_CLASS_ID,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC, $data);
        /* link Rate RU6 with Rule #1 RU */
        $data = [
            Cfg::E_TAX_CALC_A_RATE_ID => self::DEF_TAX_RATE_ID_RU6,
            Cfg::E_TAX_CALC_A_RULE_ID => self::DEF_TAX_RULE_ID_RU1,
            Cfg::E_TAX_CALC_A_CUST_TAX_CLASS_ID => self::DEF_CUST_TAX_CLASS_ID,
            Cfg::E_TAX_CALC_A_PROD_TAX_CLASS_ID => self::DEF_PROD_TAX_CLASS_ID,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC, $data);
        /* link Rate RU12 with Rule #2 RU */
        $data = [
            Cfg::E_TAX_CALC_A_RATE_ID => self::DEF_TAX_RATE_ID_RU12,
            Cfg::E_TAX_CALC_A_RULE_ID => self::DEF_TAX_RULE_ID_RU2,
            Cfg::E_TAX_CALC_A_CUST_TAX_CLASS_ID => self::DEF_CUST_TAX_CLASS_ID,
            Cfg::E_TAX_CALC_A_PROD_TAX_CLASS_ID => self::DEF_PROD_TAX_CLASS_ID,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC, $data);
    }

    /**
     * Add 3 rates records into the empty table (IDs: 1,2,3).
     */
    protected function addRates()
    {
        $data = [
            Cfg::E_TAX_CALC_RATE_A_ID => self::DEF_TAX_RATE_ID_LV,
            Cfg::E_TAX_CALC_RATE_A_COUNTRY_ID => 'LV',
            Cfg::E_TAX_CALC_RATE_A_REGION_ID => 0,
            Cfg::E_TAX_CALC_RATE_A_POSTCODE => '*',
            Cfg::E_TAX_CALC_RATE_A_CODE => 'LV Tax 21%',
            Cfg::E_TAX_CALC_RATE_A_RATE => 21
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RATE, $data);

        $data = [
            Cfg::E_TAX_CALC_RATE_A_ID => self::DEF_TAX_RATE_ID_RU6,
            Cfg::E_TAX_CALC_RATE_A_COUNTRY_ID => 'RU',
            Cfg::E_TAX_CALC_RATE_A_REGION_ID => 0,
            Cfg::E_TAX_CALC_RATE_A_POSTCODE => '*',
            Cfg::E_TAX_CALC_RATE_A_CODE => 'RU Tax 6%',
            Cfg::E_TAX_CALC_RATE_A_RATE => 6
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RATE, $data);

        $data = [
            Cfg::E_TAX_CALC_RATE_A_ID => self::DEF_TAX_RATE_ID_RU12,
            Cfg::E_TAX_CALC_RATE_A_COUNTRY_ID => 'RU',
            Cfg::E_TAX_CALC_RATE_A_REGION_ID => 0,
            Cfg::E_TAX_CALC_RATE_A_POSTCODE => '*',
            Cfg::E_TAX_CALC_RATE_A_CODE => 'RU Tax 12%',
            Cfg::E_TAX_CALC_RATE_A_RATE => 12
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RATE, $data);
    }

    /**
     * Add 2 rules records into the empty table (IDs: 1,2).
     */
    protected function addRules()
    {
        /* add Rule for LV */
        $data = [
            Cfg::E_TAX_CALC_RULE_A_ID => self::DEF_TAX_RULE_ID_LV,
            Cfg::E_TAX_CALC_RULE_A_CODE => 'LV',
            Cfg::E_TAX_CALC_RULE_A_PRIORITY => 0,
            Cfg::E_TAX_CALC_RULE_A_POSITION => 0,
            Cfg::E_TAX_CALC_RULE_A_CALC_SUBTOTAL => 0,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RULE, $data);
        /* add Rule #1 for RU */
        $data = [
            Cfg::E_TAX_CALC_RULE_A_ID => self::DEF_TAX_RULE_ID_RU1,
            Cfg::E_TAX_CALC_RULE_A_CODE => 'RU #1',
            Cfg::E_TAX_CALC_RULE_A_PRIORITY => 0,
            Cfg::E_TAX_CALC_RULE_A_POSITION => 0,
            Cfg::E_TAX_CALC_RULE_A_CALC_SUBTOTAL => 0,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RULE, $data);
        /* add Rule #2 for RU */
        $data = [
            Cfg::E_TAX_CALC_RULE_A_ID => self::DEF_TAX_RULE_ID_RU2,
            Cfg::E_TAX_CALC_RULE_A_CODE => 'RU #2',
            Cfg::E_TAX_CALC_RULE_A_PRIORITY => 0,
            Cfg::E_TAX_CALC_RULE_A_POSITION => 0,
            Cfg::E_TAX_CALC_RULE_A_CALC_SUBTOTAL => 0,
        ];
        $this->repoGeneric->addEntity(Cfg::ENTITY_MAGE_TAX_CALC_RULE, $data);

    }

    protected function clearDbData()
    {
        $this->repoGeneric->deleteEntity(Cfg::ENTITY_MAGE_TAX_CALC_RATE);
        $this->repoGeneric->deleteEntity(Cfg::ENTITY_MAGE_TAX_CALC_RULE);
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $this->checkAreaCode();
        $def = $this->manTrans->begin();
        try {
            $this->clearDbData();
            $this->addRates();
            $this->addRules();
            $this->addLinks();
            $this->manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->manTrans->end($def);
        }
        $output->writeln('<info>Command is completed.<info>');
    }

}