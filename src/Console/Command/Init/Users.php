<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init;

/**
 * Create admin and API users for tests (tester & odoo).
 */
class Users
    extends \Praxigento\App\Generic2\Console\Command\Init\Base
{
    /** @var Sub\AclUser */
    protected $subAclUsers;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\App\Generic2\Console\Command\Init\Sub\AclUser $subAclUsers

    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init-users',
            'Create admin and API users for tests (tester & odoo).'
        );
        $this->subAclUsers = $subAclUsers;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->subAclUsers->createAclUsers($output);
    }

}