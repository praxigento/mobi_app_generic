<?php
/**
 * Get all categories from catalog and enable its.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;

use Praxigento\App\Generic2\Config as Cfg;

class Categories
{
    const DEF_STORE_VIEW_ID_ADMIN = 0;
    /* EAV attr ID for catalog_category.is_active (see 'eav_attribute' table) */
    const DEF_CATEGORY_EAV_ID_IS_ACTIVE = 46;

    protected $_mageManCategory;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $_mageRepoCategory;
    /** @var  \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Magento\Catalog\Api\CategoryManagementInterface $mageManCat,
        \Magento\Catalog\Api\CategoryRepositoryInterface $mageRepoCat,
        \Praxigento\Core\Repo\IGeneric $repoGeneric

    ) {
        $this->_mageManCategory = $mageManCat;
        $this->_mageRepoCategory = $mageRepoCat;
        $this->_repoGeneric = $repoGeneric;
    }

    public function enableForAllStoreViews()
    {
        /* delete all store views data except admin */
        $entity = Cfg::ENTITY_MAGE_CATALOG_CATEGORY_EAV_INT;
        $where = Cfg::E_CATCAT_EAV_INT_STORE_ID . '>' . self::DEF_STORE_VIEW_ID_ADMIN;
        $this->_repoGeneric->deleteEntity($entity, $where);
        /* enable all categories */
        $bind = [Cfg::E_CATCAT_EAV_INT_VALUE => 1];
        $where = Cfg::E_CATCAT_EAV_INT_ATTR_ID . '=' . self::DEF_CATEGORY_EAV_ID_IS_ACTIVE;
        $this->_repoGeneric->updateEntity($entity, $bind, $where);
    }

}