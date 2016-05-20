<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Replicate;

use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\ObjectManagerProvider;
use Praxigento\Odoo\Service\IReplicate;
use Praxigento\Odoo\Service\Replicate\Request\ProductSave as ProductSaveRequest;
use Praxigento\Odoo\Service\Replicate\Response\ProductSave as ProductSaveResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Products extends Command
{
    /**
     * #@+
     * Arguments names
     */
    const ARG_IDS = 'ids';
    /**#@- */
    /** @var IReplicate */
    protected $_callReplicate;
    /** @var ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        ObjectManagerInterface $manObj,
        IReplicate $callReplicate
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_callReplicate = $callReplicate;
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
        /* call service operation */
        /** @var ProductSaveRequest $req */
        $req = $this->_manObj->create(ProductSaveRequest::class);
        /** @var ProductSaveResponse $resp */
        $resp = $this->_callReplicate->productSave($req);
        if ($resp->isSucceed()) {
            $output->writeln('<info>Init is done.<info>');
        } else {
            $output->writeln('<info>Init is failed.<info>');
        }
    }
}