<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Init;

/**
 * Create sale orders to calculate bonus and collect internal money on customer accounts.
 */
class SaleOrders
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var array SKU and count for order items (all orders have the same items). */
    protected $DATA_ORDER_ITEMS = ['10674' => 1, '215' => 2];
    /** @var  \Praxigento\App\Generic2\Cli\Init\Sub\SaleOrder */
    protected $_subSaleOrder;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\App\Generic2\Cli\Init\Sub\SaleOrder\Proxy $subSaleOrder
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init:orders',
            'Create orders to calculate bonus after that.'
        );
        $this->_manTrans = $manTrans;
        $this->_subSaleOrder = $subSaleOrder;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->_manTrans->begin();
        try {
            $customers = $this->_subSaleOrder->getAllCustomers();
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customerData */
            foreach ($customers as $customerData) {
                $mail = $customerData->getEmail();
                if ($mail != \Praxigento\Accounting\Config::CUSTOMER_REPRESENTATIVE_EMAIL) {
                    $this->_subSaleOrder->addOrder($customerData, $this->DATA_ORDER_ITEMS);
                }
            }
            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }

}