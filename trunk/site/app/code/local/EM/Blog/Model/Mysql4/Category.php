<?php

class EM_Blog_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the blog_id refers to the key field in your database table.
        $this->_init('blog/category', 'id');
    }

    public function getProductsPosition($category)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('blog_category_post'), array('post_id','cat_id'))
            ->where('cat_id=?', $category->getId());
        $positions = $this->_getWriteAdapter()->fetchPairs($select);
        return $positions;
    }
    public function _getWriteAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    public function getChildrenAmount($category)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('blog_category'), 'count(id)')
            ->where('parent_id=?', $category->getId());
        $sum = $this->_getWriteAdapter()->fetchCol($select);
        return $sum[0];
    }

}