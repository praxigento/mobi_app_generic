<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

/**
 * Create sale orders to calculate bonus and collect internal money on customer accounts.
 */
class SaleOrders
    extends \Praxigento\App\Generic2\Console\Command\Init\Base
{
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init-orders',
            'Create orders to calculate bonus after that.'
        );
        $this->_manTrans = $manTrans;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->_manTrans->begin();
        try {

            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }

}