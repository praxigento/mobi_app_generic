<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Init;

/**
 * Create admin and API users for tests (tester & odoo).
 */
class Users
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\App\Generic2\Cli\Init\Users\Create */
    protected $subCreate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\App\Generic2\Cli\Init\Users\Create $subCreate

    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init:users',
            'Create admin and API users for tests (tester & odoo).'
        );
        $this->subCreate = $subCreate;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->subCreate->createAclUsers($output);
    }

}