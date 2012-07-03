<?php
class EM_Blog_Block_Post_Recent extends Mage_Core_Block_Template
{
    public function getRecentPost()
    {
        $store_id = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('blog/post')->getCollection();
		$collection->distinct(true);
        $collection->getSelect()
                   ->join(
                            array('bps'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')),
                            "main_table.status=1
                            AND (bps.store_id=$store_id or bps.store_id=0)
                            AND bps.post_id=main_table.id",
                           array()
                  )->join(
                        array('user_table'=>Mage::getSingleton('core/resource')->getTableName('admin_user')),
                        'user_table.user_id=main_table.post_by'
                         ,
                        array('firstname'=>'firstname','lastname'=>'lastname')
                  )->order(array('main_table.post_on desc'))
                   ->limit((int)Mage::getStoreConfig('blog/info/limit_recent_post'));

        return $collection;
    }

}

