<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Cmd\Init\Users;

use \Praxigento\Core\Service\Init\Admin\User\Create as ProcCreate;

/**
 * Create Admin Users with predefined ACL role.
 */
class Create
{
    const A_EMAIL = 'email';
    const A_NAME_USER = 'name';
    const A_NAME_FIRST = 'first';
    const A_NAME_LAST = 'last';
    const A_PASSWORD = 'password';
    const ROLE_ADMIN_ID = 1;
    const USER_ODOO_NAME = 'odoo';
    const USER_ODOO_PASSWORD = '8sxUwQ5NsK2R5RUb';
    const USER_TESTER_NAME = 'tester';
    const USER_TESTER_PASSWORD = 'b4yHm6PNQ1PXeFsHzUDu';

    /** @var \Praxigento\Core\Service\Init\Admin\User\Create */
    protected $prcUserCreate;

    /**
     * Hardcoded data for initial test users.
     */
    protected $users = [
        [
            self::A_NAME_USER => 'odoo',
            self::A_PASSWORD => '8sxUwQ5NsK2R5RUb',
            self::A_EMAIL => 'mobi-odoo@praxigento.com',
            self::A_NAME_FIRST => 'Odoo',
            self::A_NAME_LAST => 'User'
        ],
        [
            self::A_NAME_USER => 'tester',
            self::A_PASSWORD => 'b4yHm6PNQ1PXeFsHzUDu',
            self::A_EMAIL => 'mobi-tester@praxigento.com',
            self::A_NAME_FIRST => 'PhantomJS',
            self::A_NAME_LAST => 'User'
        ]
    ];

    public function __construct(
        \Magento\User\Model\UserFactory $factoryUser,
        \Praxigento\Core\Service\Init\Admin\User\Create $prcUserCreate
    ) {
        $this->prcUserCreate = $prcUserCreate;
    }


    /**
     * Create all users (admin & API).
     *
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     */
    public function createAclUsers(\Symfony\Component\Console\Output\OutputInterface $output = null)
    {

        foreach ($this->users as $user) {
            $username = $user[self::A_NAME_USER];
            $output->writeln("Create user '$username'...");
            /* create context for process */
            $ctx = new \Flancer32\Lib\Data();
            $ctx->set(ProcCreate::OPT_USER_NAME, $username);
            $ctx->set(ProcCreate::OPT_PASSWORD, $user[self::A_PASSWORD]);
            $ctx->set(ProcCreate::OPT_EMAIL, $user[self::A_EMAIL]);
            $ctx->set(ProcCreate::OPT_NAME_FIRST, $user[self::A_NAME_FIRST]);
            $ctx->set(ProcCreate::OPT_NAME_LAST, $user[self::A_NAME_LAST]);
            $ctx->set(ProcCreate::OPT_ROLE_ID, self::ROLE_ADMIN_ID);

            /* perform action */
            $this->prcUserCreate->exec($ctx);
            $isCreated = $ctx->get(ProcCreate::RES_CREATED_AS_NEW);

            /* analyze results */
            if ($isCreated) {
                $output->writeln("User '$username' is created.");
            } else {
                $output->writeln("User '$username' already exists.");
            }
        }

    }
}