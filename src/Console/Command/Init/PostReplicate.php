<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

/**
 * Post replication routines:
 *  - enable all categories;
 *  - create ACL User for Odoo Replication;
 */
class PostReplicate
    extends \Praxigento\App\Generic2\Console\Command\Init\Base
{
    /** @var Sub\Categories */
    protected $_subCats;
    protected $_subAclUser;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        Sub\Categories $subCats,
        Sub\AclUser $subAclUser
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:post-replicate',
            'Enable data after replication from Odoo.'
        );
        $this->_subCats = $subCats;
        $this->_subAclUser = $subAclUser;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* enable categories after replication */
        $this->_subCats->enableForAllStoreViews();
        /* create ACL user for Odoo push replication */
        $this->_subAclUser->createAclUsers();
    }

}