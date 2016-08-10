<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;


class AclUser
{
    const USER_ODOO_NAME = 'odoo';
    const USER_ODOO_PASSWORD = '8sxUwQ5NsK2R5RUb';
    const ROLE_ADMIN_ID = 1;
    /** @var \Magento\User\Model\UserFactory */
    protected $_factoryUser;

    public function __construct(
        \Magento\User\Model\UserFactory $factoryUser
    ) {
        $this->_factoryUser = $factoryUser;
    }

    public function createAclUsers()
    {
        $userOdoo = $this->_factoryUser->create();
        $userOdoo->loadByUsername(self::USER_ODOO_NAME);
        if (self::USER_ODOO_NAME != $userOdoo->getName()) {
            $userOdoo->setFirstName('Odoo');
            $userOdoo->setLastName('Replication');
            $userOdoo->setUserName(self::USER_ODOO_NAME);
            $userOdoo->setPassword(self::USER_ODOO_PASSWORD);
            $userOdoo->setEmail('support@praxigento.com');
            $userOdoo->setRoleId(self::ROLE_ADMIN_ID);
            $userOdoo->save();
        }
    }
}