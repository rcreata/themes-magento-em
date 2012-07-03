<?php
class EM_Blog_Block_Comment extends Mage_Core_Block_Template
{   
    public function getRecentComment()     
    { 
    	/*$firstname = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');
        $lastname = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname');  */
        $collection = Mage::getModel('blog/comment')->getCollection();
		$collection->distinct(true);
                        //->addFieldToFilter('status',array('gt'=>1-Mage::getStoreConfig('blog/info/show_comment_pending')));
        $limit = Mage::getStoreConfig('blog/info/limit_comment_recent');
        $con = 1 - Mage::getStoreConfig('blog/info/show_comment_pending');
        $collection->getSelect()
             ->join(
                 array('post'=>Mage::getSingleton('core/resource')->getTableName('blog_post')),
                 'main_table.post_id=post.id
                  AND main_table.status_comment>'.$con,
                 array('title_post'=>'post.title','url'=>'post.post_identifier')
             )
			 ->join(
				array('post_store'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')),
				'post_store.post_id=post.id AND post_store.store_id in (0,'.Mage::app()->getStore()->getId().')',
				array()
			 )->limit($limit);
    	$collection ->setOrder('time', 'desc'); 
		return $collection;
    }

    public function getTitleComment($text, $length) {
       $length = abs((int)$length);
       if(strlen($text) > $length) {
          $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
       }
       return($text);
    }

}