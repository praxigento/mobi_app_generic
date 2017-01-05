<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Init\Sub;


class AclUser
{
    const RESULT_CREATED = '10';
    const RESULT_EXISTS = '20';
    const RESULT_UNDEF = '-1';
    const ROLE_ADMIN_ID = 1;
    const USER_ODOO_NAME = 'odoo';
    const USER_ODOO_PASSWORD = '8sxUwQ5NsK2R5RUb';
    const USER_TESTER_NAME = 'tester';
    const USER_TESTER_PASSWORD = 'b4yHm6PNQ1PXeFsHzUDu';
    /** @var \Magento\User\Model\UserFactory */
    protected $_factoryUser;

    public function __construct(
        \Magento\User\Model\UserFactory $factoryUser
    ) {
        $this->_factoryUser = $factoryUser;
    }

    /**
     * Internal method to create one user or check existance.
     *
     * @param $username
     * @param $first
     * @param $last
     * @param $password
     * @param $email
     * @param $roleId
     * @return string
     */
    public function _createOneUser($username, $first, $last, $password, $email, $roleId)
    {
        $result = self::RESULT_UNDEF;
        $userOdoo = $this->_factoryUser->create();
        $userOdoo->loadByUsername($username);
        if ($username != $userOdoo->getUserName()) {
            $userOdoo->setFirstName($first);
            $userOdoo->setLastName($last);
            $userOdoo->setUserName($username);
            $userOdoo->setPassword($password);
            $userOdoo->setEmail($email);
            $userOdoo->setRoleId($roleId);
            $userOdoo->save();
            $result = self::RESULT_CREATED;
        } else {
            $result = self::RESULT_EXISTS;
        }
        return $result;
    }

    /**
     * Create all users (admin & API).
     *
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     */
    public function createAclUsers(\Symfony\Component\Console\Output\OutputInterface $output = null)
    {
        /** Create user 'tester' */
        $username = self::USER_TESTER_NAME;
        $output->writeln("Create user '$username'...");
        $res = $this->_createOneUser(
            $username,
            'Phantomjs',
            'Tester',
            self::USER_TESTER_PASSWORD,
            'mobi-tester@praxigento.com',
            self::ROLE_ADMIN_ID
        );
        if ($res == self::RESULT_CREATED) {
            $output->writeln("User '$username' is created.");
        } elseif ($res == self::RESULT_EXISTS) {
            $output->writeln("User '$username' already exists.");
        } else {
            $output->writeln("Cannot create user '$username'. Shit happens.");
        }
        /** Create user 'odoo' */
        $username = self::USER_ODOO_NAME;
        $output->writeln("Create user '$username'...");
        $res = $this->_createOneUser(
            $username,
            'Odoo',
            'Access',
            self::USER_ODOO_PASSWORD,
            'mobi-odoo@praxigento.com',
            self::ROLE_ADMIN_ID
        );
        if ($res == self::RESULT_CREATED) {
            $output->writeln("User '$username' is created.");
        } elseif ($res == self::RESULT_EXISTS) {
            $output->writeln("User '$username' already exists.");
        } else {
            $output->writeln("Cannot create user '$username'. Shit happens.");
        }
    }
}