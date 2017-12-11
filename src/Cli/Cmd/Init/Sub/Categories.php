<?php
/**
 * Get all categories from catalog and enable its.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Cli\Cmd\Init\Sub;

use Praxigento\App\Generic2\Config as Cfg;

class Categories
{
    const DEF_CATEGORY_EAV_ID_IS_ACTIVE = 46;
    /* EAV attr ID for catalog_category.is_active (see 'eav_attribute' table) */
    const DEF_STORE_VIEW_ID_ADMIN = 0;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $repoCategory;
    /** @var  \Praxigento\Core\App\Repo\IGeneric */
    protected $repoGeneric;

    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCat,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric

    ) {
        $this->repoCategory = $repoCat;
        $this->repoGeneric = $repoGeneric;
    }

    public function enableForAllStoreViews()
    {
        /* delete all store views data except admin */
        $entity = Cfg::ENTITY_MAGE_CATALOG_CATEGORY_EAV_INT;
        $where = Cfg::E_CATCAT_EAV_INT_STORE_ID . '>' . self::DEF_STORE_VIEW_ID_ADMIN;
        $this->repoGeneric->deleteEntity($entity, $where);
        /* enable all categories */
        $bind = [Cfg::E_CATCAT_EAV_INT_VALUE => 1];
        $where = Cfg::E_CATCAT_EAV_INT_ATTR_ID . '=' . self::DEF_CATEGORY_EAV_ID_IS_ACTIVE;
        $this->repoGeneric->updateEntity($entity, $bind, $where);
    }

}