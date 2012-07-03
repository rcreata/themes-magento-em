<?php

class EM_Blog_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the blog_id refers to the key field in your database table.
        $this->_init('blog/tag', 'id');
    }
}
