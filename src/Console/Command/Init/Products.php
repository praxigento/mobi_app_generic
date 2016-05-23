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

class Products extends Command
{
    /** @var \Praxigento\Odoo\Service\IReplicate */
    protected $_callReplicate;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Magento\Framework\Webapi\ServiceInputProcessor */
    protected $_serviceInputProcessor;
    /** @var Sub\Init */
    protected $_subInit;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Framework\Webapi\ServiceInputProcessor $serviceInputProcessor,
        \Praxigento\Odoo\Service\IReplicate $callReplicate,
        Sub\Init $subInit
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_serviceInputProcessor = $serviceInputProcessor;
        $this->_callReplicate = $callReplicate;
        $this->_subInit = $subInit;
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
        $this->setName('prxgt:app:init-products');
        $this->setDescription('Create sample products in application.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* setup session */
        $this->_setAreaCode();
        /* load JSON data */
        $fileData = file_get_contents(__DIR__ . '/data.json');
        $jsonData = json_decode($fileData, true);
        $bundle = $this->_serviceInputProcessor->convertValue($jsonData['data'],
            \Praxigento\Odoo\Data\Api\IBundle::class);
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* create warehouse */
            $this->_subInit->warehouse();
            /* call service operation */
            /** @var ProductSaveRequest $req */
            $req = $this->_manObj->create(ProductSaveRequest::class);
            $req->setProductBundle($bundle);
            $this->_callReplicate->productSave($req);
            $this->_manTrans->transactionCommit($trans);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
    }

}