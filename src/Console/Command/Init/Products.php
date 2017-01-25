<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

use Praxigento\Odoo\Service\Replicate\Request\ProductSave as ProductSaveRequest;

class Products
    extends \Praxigento\Core\Console\Command\Base
{
    /** @var \Praxigento\Odoo\Service\IReplicate */
    protected $_callReplicate;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Magento\Framework\Webapi\ServiceInputProcessor */
    protected $_serviceInputProcessor;
    /** @var Sub\Categories */
    protected $_subCats;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Framework\Webapi\ServiceInputProcessor $serviceInputProcessor,
        \Praxigento\Odoo\Service\IReplicate $callReplicate,
        Sub\Categories $subCats
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init-products',
            'Create sample products in application.'
        );
        $this->_manTrans = $manTrans;
        $this->_serviceInputProcessor = $serviceInputProcessor;
        $this->_callReplicate = $callReplicate;
        $this->_subCats = $subCats;
    }


    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* load JSON data */
        $fileData = file_get_contents(__DIR__ . '/data.json');
        $jsonData = json_decode($fileData, true);
        $bundle = $this->_serviceInputProcessor->convertValue($jsonData['data'],
            \Praxigento\Odoo\Data\Odoo\Inventory::class);
        $def = $this->_manTrans->begin();
        try {
            /* create products using replication */
            /** @var ProductSaveRequest $req */
            $req = $this->_manObj->create(ProductSaveRequest::class);
            $req->setProductBundle($bundle);
            $this->_callReplicate->productSave($req);
            /* enable categories after replication */
            $this->_subCats->enableForAllStoreViews();
            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }

}