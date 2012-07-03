<?php

class EM_Blog_Model_Mysql4_Post extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the blog_id refers to the key field in your database table.
        $this->_init('blog/post', 'id');
    }

    public function getCategoryIds($post)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('blog_category_post'), 'cat_id')
            ->where('post_id=?', $post->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }
}