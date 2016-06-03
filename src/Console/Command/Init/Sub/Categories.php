<?php
/**
 * Get all categories from catalog and enable its.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init\Sub;


class Categories
{
    protected $_mageManCategory;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $_mageRepoCategory;

    public function __construct(
        \Magento\Catalog\Api\CategoryManagementInterface $mageManCat,
        \Magento\Catalog\Api\CategoryRepositoryInterface $mageRepoCat

    ) {
        $this->_mageManCategory = $mageManCat;
        $this->_mageRepoCategory = $mageRepoCat;
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterface $tree
     */
    public function enable($tree = null)
    {
        if (is_null($tree)) {
            /* get root category*/
            $tree = $this->_mageManCategory->getTree();
        }
        /* check if category is active */
        if (!$tree->getIsActive()) {
            $id = $tree->getId();
            /** @var \Magento\Catalog\Api\Data\CategoryInterface $cat */
            $cat = $this->_mageRepoCategory->get($id);
            $cat->setIsActive(true);
            $this->_mageRepoCategory->save($cat);
        }
        /* process subtree*/
        $subTree = $tree->getChildrenData();
        foreach ($subTree as $item) {
            $this->enable($item);
        }

    }
}