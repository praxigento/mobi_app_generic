<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Helper\Odoo;

use Praxigento\Accounting\Repo\Data\Type\Operation as ETypeOper;
use Praxigento\App\Generic2\Config as Cfg;

/**
 * Implementation of the codes manager for Generic MOBI application.
 */
class BusinessCodesManager
    implements \Praxigento\Odoo\Tool\IBusinessCodesManager
{
    /**#@+
     * Business codes for Customer Groups.
     *
     * _CYR:
     * https://jira.prxgt.com/browse/MOBI-762?focusedCommentId=95308&page=com.atlassian.jira.plugin.system.issuetabpanels:comment-tabpanel#comment-95308
     */
    const B_CUST_GROUP_ANONYMOUS = 'anon';
    const B_CUST_GROUP_DISTRIBUTOR = 'distributor';
    const B_CUST_GROUP_DISTRIBUTOR_CYR = 'distributоr';
    const B_CUST_GROUP_PRIVILEGED = 'privileged';
    const B_CUST_GROUP_PRIVILEGED_CYR = 'privilegеd';
    const B_CUST_GROUP_REFERRAL = 'referral';
    const B_CUST_GROUP_RETAIL = 'retail';
    /**#@- */

    /**#@+
     * Business codes for Operations (transactions).
     */
    const B_OPER_MANUAL = 'MANUAL';
    const B_OPER_SALE_PAYMENT = 'SALE_PAYMENT';
    /**#@- */

    /**#@+
     * Business codes for Payment Methods.
     */
    const B_PAY_BRAINTREE = 'ccard_braintree';
    const B_PAY_CHECK_MONEY = 'check_money';
    const B_PAY_INTERNAL_MONEY = 'internal_money';
    /**#@+
     * Business codes for Shipping Methods.
     */
    const B_SHIP_FLAT_RATE = 'flat_rate';
    /**#@- */

    /**#@+
     * Magento codes for Carriers (shipping methods).
     */
    const M_CARRIER_FLAT_RATE = 'flatrate';
    /**#@- */

    /**#@+
     * Magento IDs for Customer Groups.
     */
    const M_CUST_GROUP_ANONYMOUS = 0;
    const M_CUST_GROUP_DISTRIBUTOR = 1;
    const M_CUST_GROUP_PRIVILEGED = 2;
    const M_CUST_GROUP_REFERRAL = 4;
    const M_CUST_GROUP_RETAIL = 3;
    /**#@- */

    /**#@+
     * Magento codes for Payment Methods.
     */
    const M_PAY_BRAINTREE = 'braintree';
    const M_PAY_CHECK_MONEY = 'checkmo';
    const M_PAY_INTERNAL_MONEY = 'praxigento_wallet';
    /**#@- */

    /**#@+
     * Magento codes for Shipping Methods.
     */
    const M_SHIP_FLAT_RATE = 'flatrate_flatrate';
    /**#@- */

    /**#@+
     * Magento codes for tracking titles.
     */
    const M_TRACK_TITLE__FLAT_RATE = 'Pseudo tracking code for tests.';
    /**#@- */

    /**
     * Map to convert operation type code into operation type id.
     *
     * @var string[]
     */
    private $cacheOperTypeCodesById;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Operation */
    private $daoTypeOper;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Type\Operation $daoTypeOper
    ) {
        $this->daoTypeOper = $daoTypeOper;
    }

    public function getBusCodeForCustomerGroupById($groupId)
    {
        $result = self::B_CUST_GROUP_RETAIL;
        if ($groupId == self::M_CUST_GROUP_DISTRIBUTOR) {
            $result = self::B_CUST_GROUP_DISTRIBUTOR;
        } elseif ($groupId == self::M_CUST_GROUP_PRIVILEGED) {
            $result = self::B_CUST_GROUP_PRIVILEGED;
        } elseif ($groupId == self::M_CUST_GROUP_REFERRAL) {
            $result = self::B_CUST_GROUP_REFERRAL;
        } elseif ($groupId == self::M_CUST_GROUP_ANONYMOUS) {
            $result = self::B_CUST_GROUP_ANONYMOUS;
        }
        return $result;
    }

    public function getBusCodeForOperTypeId($typeId)
    {
        $result = null;
        $typeCode = $this->mapOperTypeIdToCode($typeId);
        if (
            ($typeCode == Cfg::CODE_TYPE_OPER_CHANGE_BALANCE) ||
            ($typeCode == Cfg::CODE_TYPE_OPER_WALLET_TRANSFER)
        ) {
            $result = self::B_OPER_MANUAL;
        } elseif ($typeCode == Cfg::CODE_TYPE_OPER_WALLET_SALE) {
            $result = self::B_OPER_SALE_PAYMENT;
        }
        return $result;
    }

    public function getBusCodeForPaymentMethod(\Magento\Sales\Api\Data\OrderPaymentInterface $payment)
    {
        $result = null;
        $mage = $payment->getMethod();
        if ($mage == self::M_PAY_BRAINTREE) {
            $result = self::B_PAY_BRAINTREE;
        } elseif ($mage == self::M_PAY_CHECK_MONEY) {
            $result = self::B_PAY_CHECK_MONEY;
        } elseif ($mage == self::M_PAY_INTERNAL_MONEY) {
            $result = self::B_PAY_INTERNAL_MONEY;
        }
        return $result;
    }

    public function getBusCodeForShippingMethod(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = null;
        $mage = $order->getShippingMethod();
        if ($mage == self::M_SHIP_FLAT_RATE) {
            $result = self::B_SHIP_FLAT_RATE;
        }
        return $result;
    }

    public function getMagCodeForCarrier($businessCode)
    {
        $result = 'unknown carrier';
        if ($businessCode == self::B_SHIP_FLAT_RATE) {
            $result = self::M_CARRIER_FLAT_RATE;
        }
        return $result;
    }

    public function getMageIdForCustomerGroupByCode($groupCode)
    {
        $result = null;
        if ($groupCode == self::B_CUST_GROUP_DISTRIBUTOR || $groupCode == self::B_CUST_GROUP_DISTRIBUTOR_CYR) {
            $result = self::M_CUST_GROUP_DISTRIBUTOR;
        } elseif ($groupCode == self::B_CUST_GROUP_PRIVILEGED || $groupCode == self::B_CUST_GROUP_PRIVILEGED_CYR) {
            $result = self::M_CUST_GROUP_PRIVILEGED;
        } elseif ($groupCode == self::B_CUST_GROUP_REFERRAL) {
            $result = self::M_CUST_GROUP_REFERRAL;
        } elseif ($groupCode == self::B_CUST_GROUP_ANONYMOUS) {
            $result = self::M_CUST_GROUP_ANONYMOUS;
        }
        return $result;
    }

    public function getTitleForCarrier($businessCode)
    {
        $result = 'unknown tracking';
        if ($businessCode == self::B_SHIP_FLAT_RATE) {
            $result = self::M_TRACK_TITLE__FLAT_RATE;
        }
        return $result;
    }

    private function mapOperTypeIdToCode($typeId)
    {
        if (is_null($this->cacheOperTypeCodesById)) {
            $this->cacheOperTypeCodesById = [];
            $rs = $this->daoTypeOper->get();
            /** @var ETypeOper $one */
            foreach ($rs as $one) {
                $id = $one->getId();
                $code = $one->getCode();
                $this->cacheOperTypeCodesById[$id] = $code;
            }
        }
        $result = $this->cacheOperTypeCodesById[$typeId];
        return $result;
    }
}