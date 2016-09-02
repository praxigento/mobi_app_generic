<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

use Praxigento\App\Generic2\Config as Cfg;
use Praxigento\App\Generic2\Tool\Odoo\Def\BusinessCodesManager;

class CustomerGroups
{
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        $this->_repoGeneric = $repoGeneric;
    }

    public function renameGroups()
    {
        $entity = Cfg::ENTITY_MAGE_CUSTOMER_GROUP;
        /* retail */
        $id = [Cfg::E_CUSTGROUP_A_ID => BusinessCodesManager::M_CUST_GROUP_RETAIL];
        $bind = [Cfg::E_CUSTGROUP_A_CODE => BusinessCodesManager::B_CUST_GROUP_RETAIL];
        $this->_repoGeneric->updateEntityById($entity, $bind, $id);
        /* distributor */
        $id = [Cfg::E_CUSTGROUP_A_ID => BusinessCodesManager::M_CUST_GROUP_DISTRIBUTOR];
        $bind = [Cfg::E_CUSTGROUP_A_CODE => BusinessCodesManager::B_CUST_GROUP_DISTRIBUTOR];
        $this->_repoGeneric->updateEntityById($entity, $bind, $id);
        /* wholesaler */
        $id = [Cfg::E_CUSTGROUP_A_ID => BusinessCodesManager::M_CUST_GROUP_WHOLESALE];
        $bind = [Cfg::E_CUSTGROUP_A_CODE => BusinessCodesManager::B_CUST_GROUP_WHOLESALE];
        $this->_repoGeneric->updateEntityById($entity, $bind, $id);
    }
}