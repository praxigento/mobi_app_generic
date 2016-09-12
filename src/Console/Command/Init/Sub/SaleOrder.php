<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

class SaleOrder
{
    const STOCK_ID = 2;
    /** @var \Magento\Sales\Api\OrderManagementInterface */
    protected $_apiMgrOrder;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Magento\Quote\Model\QuoteManagement */
    protected $_manQuote;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_manStore;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $_repoCatProd;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $_repoCust;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\OrderManagementInterface $apiMgrOrder,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Quote\Model\QuoteManagement $manQuote,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoCatProd,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        $this->_manObj = $manObj;
        $this->_apiMgrOrder = $apiMgrOrder;
        $this->_manStore = $manStore;
        $this->_manQuote = $manQuote;
        $this->_repoCatProd = $repoCatProd;
        $this->_repoCust = $repoCust;
        $this->_repoGeneric = $repoGeneric;
    }

    public function _populateQuoteAddrBilling(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Customer\Api\Data\CustomerInterface $customerDo
    ) {
        /** @var \Magento\Quote\Api\Data\AddressInterface $addr */
        $addr = $this->_manObj->create(\Magento\Quote\Api\Data\AddressInterface::class);
        $addr->setFirstname($customerDo->getFirstname());
        $addr->setLastname($customerDo->getLastname());
        $addr->setEmail($customerDo->getEmail());
        $addr->setTelephone('23344556');
        $addr->setStreet('Liela iela');
        $addr->setCity('Riga');
        $addr->setRegionId(362); // Riga region
        $addr->setPostcode('1010');
        $addr->setCountryId('LV');
        $quote->setBillingAddress($addr);
        return $quote;
    }

    public function _populateQuoteAddrShipping(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Customer\Api\Data\CustomerInterface $customerDo
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $addr */
        $addr = $this->_manObj->create(\Magento\Quote\Model\Quote\Address::class);
        $addr->setFirstname($customerDo->getFirstname());
        $addr->setLastname($customerDo->getLastname());
        $addr->setEmail($customerDo->getEmail());
        $addr->setTelephone('23344556');
        $addr->setStreet('Liela iela');
        $addr->setCity('Riga');
        $addr->setRegionId(362); // Riga region
        $addr->setPostcode('1010');
        $addr->setCountryId('LV');
        $quote->setShippingAddress($addr);
        return $quote;
    }

    public function _populateQuoteShippingMethod(\Magento\Quote\Model\Quote $quote)
    {
        /** @var \Magento\Quote\Model\Quote\Address $addr */
        $addr = $quote->getShippingAddress();
        $addr->setShippingMethod('flatrate_flatrate');
        $addr->setCollectShippingRates(true);
        $addr->collectShippingRates();
    }

    public function addOrder(
        $customer,
        $itemsData
    ) {
        $store = $this->_manStore->getStore(2);
        $storeId = $store->getId();
        $websiteId = $store->getWebsiteId();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_manObj->create(\Magento\Quote\Model\Quote::class);
        $quote->setStore($store);
//        $quote->setCurrency();
        $quote->assignCustomer($customer);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_manObj->create(\Magento\Catalog\Model\Product::class);
        $product->load(1);
        $quote->addProduct($product);
        $this->_populateQuoteAddrShipping($quote, $customer);
        $this->_populateQuoteAddrBilling($quote, $customer);
        $this->_populateQuoteShippingMethod($quote);
        $quote->setPaymentMethod('checkmo'); //payment method
        $quote->setInventoryProcessed(false); //not effect inventory
        $quote->save();
        $id = $quote->getId();
        $quote = $this->_manObj->create(\Magento\Quote\Model\Quote::class);
        $quote->load($id);
        $quote->getPayment()->setMethod('checkmo');
        $quote->collectTotals();

        // Create Order From Quote
        $order = $this->_manQuote->submit($quote);

    }

    public function getAllCustomers()
    {
        $crit = $this->_manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Customer\Api\Data\CustomerSearchResultsInterface $all */
        $all = $this->_repoCust->getList($crit);
        $result = $all->getItems();
        return $result;
    }
}