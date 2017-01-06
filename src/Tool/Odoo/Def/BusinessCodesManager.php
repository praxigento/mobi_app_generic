<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Tool\Odoo\Def;

/**
 * Implementation of the codes manager for Generic MOBI application.
 */
class BusinessCodesManager
    implements \Praxigento\Odoo\Tool\IBusinessCodesManager
{
    /**#@+
     * Business codes for Customer Groups.
     */
    const B_CUST_GROUP_DISTRIBUTOR = 'distributor';
    const B_CUST_GROUP_REFERRAL = 'referral';
    const B_CUST_GROUP_RETAIL = 'retail';
    const B_CUST_GROUP_WHOLESALE = 'wholesale';
    /**#@- */

    /**#@+
     * Business codes for Payment Methods.
     */
    const B_PAY_BRAINTREE = 'ccard_braintree';
    const B_PAY_CHECK_MONEY = 'check_money';
    const B_PAY_INTERNAL_MONEY = 'internal_money';
    /**#@- */

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
    const M_CUST_GROUP_DISTRIBUTOR = 1;
    const M_CUST_GROUP_REFERRAL = 4;
    const M_CUST_GROUP_RETAIL = 3;
    const M_CUST_GROUP_WHOLESALE = 2;
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

    public function getBusCodeForCustomerGroupById($groupId)
    {
        $result = self::B_CUST_GROUP_RETAIL;
        if ($groupId == self::M_CUST_GROUP_DISTRIBUTOR) {
            $result = self::B_CUST_GROUP_DISTRIBUTOR;
        } elseif ($groupId == self::M_CUST_GROUP_WHOLESALE) {
            $result = self::B_CUST_GROUP_WHOLESALE;
        } elseif ($groupId == self::M_CUST_GROUP_REFERRAL) {
            $result = self::B_CUST_GROUP_REFERRAL;
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

    public function getTitleForCarrier($businessCode)
    {
        $result = 'unknown tracking';
        if ($businessCode == self::B_SHIP_FLAT_RATE) {
            $result = self::M_TRACK_TITLE__FLAT_RATE;
        }
        return $result;
    }
}