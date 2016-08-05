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

    const B_PAY_BRAINTREE = 'ccard_braintree';
    const B_PAY_CHECK_MONEY = 'check_money';
    const B_SHIP_FLAT_RATE = 'flat_rate';
    const M_CARRIER_FLAT_RATE = 'flatrate';
    const M_PAY_BRAINTREE = 'braintree';
    const M_PAY_CHECK_MONEY = 'checkmo';
    const M_SHIP_FLAT_RATE = 'flatrate_flatrate';
    const M_TRACK_TITLE__FLAT_RATE = 'Pseudo tracking code for tests.';

    /** @inheritdoc */
    public function getBusCodeForPaymentMethod(\Magento\Sales\Api\Data\OrderPaymentInterface $payment)
    {
        $result = null;
        $mage = $payment->getMethod();
        if ($mage == self::M_PAY_BRAINTREE) {
            $result = self::B_PAY_BRAINTREE;
        }
        if ($mage == self::M_PAY_CHECK_MONEY) {
            $result = self::B_PAY_CHECK_MONEY;
        }
        return $result;
    }

    /** @inheritdoc */
    public function getBusCodeForShippingMethod(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = null;
        $mage = $order->getShippingMethod();
        if ($mage == self::M_SHIP_FLAT_RATE) {
            $result = self::B_SHIP_FLAT_RATE;
        }
        return $result;
    }

    /** @inheritdoc */
    public function getMagCodeForCarrier($businessCode)
    {
        $result = 'unknown carrier';
        if ($businessCode == self::B_SHIP_FLAT_RATE) {
            $result = self::M_CARRIER_FLAT_RATE;
        }
        return $result;
    }

    /** @inheritdoc */
    public function getTitleForCarrier($businessCode)
    {
        $result = 'unknown tracking';
        if ($businessCode == self::B_SHIP_FLAT_RATE) {
            $result = self::M_TRACK_TITLE__FLAT_RATE;
        }
        return $result;
    }
}