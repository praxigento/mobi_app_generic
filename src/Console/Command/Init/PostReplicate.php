<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Magento\Setup\Model\ObjectManagerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostReplicate
    extends \Symfony\Component\Console\Command\Command
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var Sub\Categories */
    protected $_subCats;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        Sub\Categories $subCats
    ) {
        parent::__construct();
        $this->_manObj = $manObj;
        $this->_subCats = $subCats;
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
        $this->setName('prxgt:odoo:post-replicate');
        $this->setDescription('Enable data after replication from Odoo.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* setup session */
        $this->_setAreaCode();
        try {
            /* enable categories after replication */
            $this->_subCats->enableForAllStoreViews();
        } finally {
        }
    }

}