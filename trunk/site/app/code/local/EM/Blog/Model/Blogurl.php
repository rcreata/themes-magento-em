<?php

class EM_Blog_Model_Blogurl extends Mage_Core_Model_Abstract
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('blog/blogurl');
    }
    
    public function load($id, $field=null){
        return $url = parent::load($id, $field);
    }
    
    /*public function getDataUrl($requestInfo)
    {
         $write = Mage::getSingleton('core/resource')->getConnection('core_write');
         $query = "select * from blog_url_rewrite where request_path = '$requestInfo' limit 1";
         return $write->fetchRow($query);
    }
    
    public function getDataByPostId($post_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select * from blog_url_rewrite where post_id = $post_id limit 1";
        return $write->fetchRow($query);
    }
    
    public function getDataByTagId($tag_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select * from blog_url_rewrite where tag_id = $tag_id limit 1";
        return $write->fetchRow($query);
    }*/

    public function validate($url,$id,$type)
    {
        $collection = $this->getCollection()->addFieldToFilter('request_path',$url.'.html');
        if($id)
            $collection->addFieldToFilter($type.'_id',array('neq'=>$id));
        return $collection->count() > 0;
    }

}