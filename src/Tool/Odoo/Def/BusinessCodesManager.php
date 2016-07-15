<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Tool\Odoo\Def;


class BusinessCodesManager
    implements \Praxigento\Odoo\Tool\IBusinessCodesManager
{

    const B_PAY_BRAINTREE = 'ccard_braintree';
    const B_PAY_CHECK_MONEY = 'check_money';
    const B_SHIP_FLAT_RATE = 'flat_rate';
    const M_PAY_BRAINTREE = 'braintree';
    const M_PAY_CHECK_MONEY = 'checkmo';
    const M_SHIP_FLAT_RATE = 'flatrate_flatrate';

    public function getPaymentMethodCode(\Magento\Sales\Api\Data\OrderPaymentInterface $payment)
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

    public function getShippingMethodCode(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = null;
        $mage = $order->getShippingMethod();
        if ($mage == self::M_SHIP_FLAT_RATE) {
            $result = self::B_SHIP_FLAT_RATE;
        }
        return $result;
    }
}