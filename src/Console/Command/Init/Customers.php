<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Magento\Setup\Model\ObjectManagerProvider;
use Praxigento\Odoo\Service\Replicate\Request\ProductSave as ProductSaveRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Customers extends Command
{
    /**
     * Downline tree default dependencies.
     * @var array
     */
    protected $DEFAULT_DWNL_TREE = [
        1 => 1,
        2 => 1,
        3 => 1,
        4 => 2,
        5 => 2,
        6 => 3,
        7 => 3,
        8 => 6,
        9 => 6,
        10 => 7,
        11 => 7,
        12 => 10,
        13 => 10
    ];

    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Magento\Customer\Model\ResourceModel\CustomerRepository */
    protected $_repoMageCustomer;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $repoMageCustomer
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_repoMageCustomer = $repoMageCustomer;
    }

    /**
     * Sets area code to start a session for replication.
     */
    private function _setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $config = $configLoader->load($areaCode);
        $this->_manObj->configure($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prxgt:app:init-customers');
        $this->setDescription('Create sample downline tree in application.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* setup session */
        $this->_setAreaCode();       
        $trans = $this->_manTrans->transactionBegin();
        try {
           
            $this->_manTrans->transactionCommit($trans);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
    }

}