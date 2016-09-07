<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

use Praxigento\App\Generic2\Config as Cfg;

class SaleOrder
{
    /** @var \Magento\Sales\Api\OrderManagementInterface */
    protected $_apiMgrOrder;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\OrderManagementInterface $apiMgrOrder,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        $this->_manObj = $manObj;
        $this->_apiMgrOrder = $apiMgrOrder;
        $this->_repoGeneric = $repoGeneric;
    }

    public function _populateOrderAddrBilling(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Customer\Api\Data\CustomerInterface $customerDo

    ) {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addr */
        $addr = $this->_manObj->create(\Magento\Sales\Api\Data\OrderAddressInterface::class);
        $addr->setFirstname($customerDo->getFirstname());
        $addr->setLastname($customerDo->getLastname());
        // $addr->setEmail($customerDo->getEmail()); // email is replaced in $order->setBillingAddress()
        $addr->setTelephone('23344556');
        $addr->setStreet('Liela iela');
        $addr->setCity('Riga');
        $addr->setPostcode('1010');
        $addr->setCountryId('LV');
        $order->setBillingAddress($addr);
        return $order;
    }

    /**
     * Populate order data with payment data.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function _populateOrderPayment(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $this->_manObj->create(\Magento\Sales\Api\Data\OrderPaymentInterface::class);
        $payment->setMethod('checkmo');
        $order->setPayment($payment);
        return $order;
    }

    public function addOrder(
        \Magento\Customer\Api\Data\CustomerInterface $customerDo,
        $itemsData
    ) {
        $order = $this->_manObj->create(\Magento\Sales\Api\Data\OrderInterface::class);
        $order->setStoreId(2);
        $order->setCustomerEmail($customerDo->getEmail());
        $this->_populateOrderPayment($order);
        $this->_populateOrderAddrBilling($order, $customerDo);
        $this->_apiMgrOrder->place($order);
    }

    public function getAllCustomers()
    {
        $result = [];
        $name = Cfg::ENTITY_MAGE_CUSTOMER;
        $all = $this->_repoGeneric->getEntities($name);
        foreach ($all as $one) {
            $mageId = $one[Cfg::E_CUSTOMER_A_ENTITY_ID];
            $customer = $this->_manObj->create(\Magento\Customer\Api\Data\CustomerInterface::class, ['data' => $one]);
            $result[$mageId] = $customer;
        }
        return $result;
    }
}