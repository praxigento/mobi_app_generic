<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

class SaleOrder
{
    const METHOD_PAYMENT = 'checkmo';
    const METHOD_SHIPPING = 'flatrate_flatrate';
    const SKU = '10674San';
    const STOCK_ID_RUS = 2;
    const STORE_ID_RUS = 3;
    const DATE_PAID = '2016-06-21 16:32:21';
    /**
     * Cache for products data from DB.
     *
     * @var array [sku=>$product]
     */
    protected $_cacheProducts = [];
    /** @var \Magento\Framework\Event\ManagerInterface */
    protected $_manEvent;
    /** @var  \Magento\Sales\Model\Service\InvoiceService */
    protected $_manInvoice;
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
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\Event\ManagerInterface $manEvent,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Quote\Model\QuoteManagement\Proxy $manQuote,
        \Magento\Sales\Api\InvoiceManagementInterface $manInvoice,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoCatProd,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale
    ) {
        $this->_manObj = $manObj;
        $this->_manEvent = $manEvent;
        $this->_manStore = $manStore;
        $this->_manQuote = $manQuote;
        $this->_manInvoice = $manInvoice;
        $this->_repoCatProd = $repoCatProd;
        $this->_repoCust = $repoCust;
        $this->_repoPvSale = $repoPvSale;
    }

    /**
     * Cached access to product in DB.
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function _getProductBySku($sku)
    {
        if (!isset($this->_cacheProducts[$sku])) {
            $this->_cacheProducts[$sku] = $this->_repoCatProd->get($sku);
        }
        return $this->_cacheProducts[$sku];
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

    public function _populateQuoteItems(
        \Magento\Quote\Model\Quote $quote
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_getProductBySku(self::SKU);
//        $product->setPrice(8.00);
        $req = new \Magento\Framework\DataObject();
        $req->setQty(1);
        $req->setCustomPrice(8);
        $quote->addProduct($product, $req);
        return $quote;
    }

    public function _populateQuotePaymentMethod(\Magento\Quote\Model\Quote $quote)
    {
        $quote->setPaymentMethod(self::METHOD_PAYMENT);
        $quote->getPayment()->setMethod(self::METHOD_PAYMENT);
    }

    public function _populateQuoteShippingMethod(\Magento\Quote\Model\Quote $quote)
    {
        /** @var \Magento\Quote\Model\Quote\Address $addr */
        $addr = $quote->getShippingAddress();
        $addr->setShippingMethod(self::METHOD_SHIPPING);
        $addr->setCollectShippingRates(true);
        $addr->collectShippingRates();
    }

    /**
     * Add one order to customer.
     *
     * @param $customer
     * @param $itemsData
     */
    public function addOrder(
        $customer,
        $itemsData
    ) {
        /* create order for Russian store/stock */
        $this->_manStore->setCurrentStore(self::STORE_ID_RUS);
        $store = $this->_manStore->getStore();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_manObj->create(\Magento\Quote\Model\Quote::class);
        $quote->setStore($store);
        $quote->assignCustomer($customer);
        $quote->setInventoryProcessed(false); //not effect inventory
        /** Populate orders with data. */
        $this->_populateQuoteItems($quote);
        $this->_populateQuoteAddrShipping($quote, $customer);
        $this->_populateQuoteAddrBilling($quote, $customer);
        $this->_populateQuoteShippingMethod($quote);
        $this->_populateQuotePaymentMethod($quote);
        /* save quote then reload it by ID to create IDs for items (see MOBI-434, $_items, $_data['items'], $_data['items_collection']) */
        $quote->collectTotals();
        $quote->save();
        $id = $quote->getId();
        $quote = $this->_manObj->create(\Magento\Quote\Model\Quote::class);
        $quote->load($id);
        $quoteItems = $quote->getItemsCollection();
        // Create Order From Quote
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->_manQuote->submit($quote);
        $items = $order->getItems();
        $item = reset($items);
        $item->setBaseOriginalPrice(8);
        $item->save();
        $order->save();

        /* register PV */
        $this->_manEvent->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
        /* prepare invoice */
        $invoice = $this->_manInvoice->prepareInvoice($order);
        $invoice->register();
        $invoice->save();
//        $invoiceId = $invoice->getEntityId();
        /* update date paid in PV register */
//        $orderId = $order->getEntityId();
//        $bind = [
//            \Praxigento\Pv\Data\Entity\Sale::ATTR_DATE_PAID => self::DATE_PAID
//        ];
//        $this->_repoPvSale->updateById($orderId, $bind);
        /* transfer PV to customer account */
//        $invoice->load($invoiceId);
//        $order->load($orderId);
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
        $this->_manEvent->dispatch('sales_order_invoice_pay', ['order' => $order, 'invoice' => $invoice]);
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getAllCustomers()
    {
        $crit = $this->_manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Customer\Api\Data\CustomerSearchResultsInterface $all */
        $all = $this->_repoCust->getList($crit);
        $result = $all->getItems();
        return $result;
    }
}