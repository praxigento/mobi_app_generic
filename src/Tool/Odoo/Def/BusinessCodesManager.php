<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Tool\Odoo\Def;


class BusinessCodesManager
    implements \Praxigento\Odoo\Tool\IBusinessCodesManager
{

    public function getShippingMethodCode(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = $order->getShippingMethod();
        return $result;
    }

    public function getPaymentMethodCode(\Magento\Sales\Api\Data\OrderPaymentInterface $payment)
    {
        $result = $payment->getMethod();
        return $result;
    }
}