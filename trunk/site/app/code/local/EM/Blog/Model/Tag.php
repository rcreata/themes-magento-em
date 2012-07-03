<?php

class EM_Blog_Model_Tag extends Mage_Core_Model_Abstract
{
	protected $maxQty = 0;
    protected $minQty = 0;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/tag');
    }
    
    public function getTags()
    {
      $collection = Mage::getResourceModel('blog/tag_collection');
      //$collection->addAttributeToSelect('name');      
      $collection->getSelect();
    }
    

    public function getTagList()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->setFetchMode(Zend_Db::FETCH_OBJ);
        $query = 'select * from '.Mage::getSingleton('core/resource')->getTableName('blog_tag').' where status = 1';
        $tags = $write->fetchAll($query);
        //print_r($emails);exit;
        $data = array();
        foreach($tags as $t)
        {
            $data[] = array('label' => $t->name,'value' => $e->id);
        }
        return $data;
    } 
    /*public function getTags($tag)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        //$write->setFetchMode(Zend_Db::FETCH_OBJ);
        $query = "select id,name from blog_tag where name like '%$tag%'";
        //print_r($write->fetchAll($query));exit;
        return $write->fetchAll($query);
    }*/
    
    public function getStoreList()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        //$write->setFetchMode(4);
        $query = "select name from ".Mage::getSingleton('core/resource')->getTableName('core_store');
        //print_r($write->fetchAll($query));exit;
        $tmp = $write->fetchAll($query);
        $storeList[0] = 'All store view';
        for($i = 1;$i<count($tmp);$i++)
        {
            $storeList[] = $tmp[$i]['name'];
        }
        //print_r($storeList);exit;
        return $storeList;
    }
    
    /*public function getStoreOfTag($id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select c.name,c.store_id
                  from blog_post_store as b,core_store as c
                  where b.store_id=c.store_id and b.post_id=$id";
        //echo $query;exit;
        $tmp = $write->fetchAll($query);
        //print_r($tmp);exit;
        $data = array();
        foreach($tmp as $t)
        {
            $data[] = $t['store_id'];   
        }
        //print_r($data);exit;
        return $data;
    }*/
    
    /*public function setTagStore($stores,$post_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "INSERT INTO blog_post_store (post_id,store_id) VALUES ";
        try {
            $write->query("delete from blog_post_store where post_id=$post_id");
        }
        catch(Exception $e)
        {
            
        }
        //print_r($stores);exit;
        if(count($stores))
        {
            foreach($stores as $s)
            {
                try{
                    $write->query($query."('$post_id','$s')");
                }
                catch(Exception $e)
                {
                  
                }
            }
        }
        //INSERT INTO `magento_3`.`blog_post_store` (`post_id`, `store_id`) VALUES ('1', '2'), ('1', '3');   
    }*/
    
    /*public function setTag($tags_id,$post_id,$status,$tags_name)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "delete from blog_tag_post where post_id=$post_id";
        $write->query($query);
        for($i = 0;$i<count($tags_id);$i++)
        {
              try{
                  if(!$tags_id[$i])//day la tab moi
                  {//echo $tags_name[$i];exit;
                        $query = "INSERT INTO blog_tag (id,name,status) VALUES (null,'$tags_name[$i]','$status')";
                        
                        $write->query($query);
                        $tmp = $write->fetchRow("select id from blog_tag where name='$tags_name[$i]' limit 1");
                        
                        $tags_id[$i] = $tmp['id'];      
                  }  
                  $query = "INSERT INTO blog_tag_post (post_id,tag_id) VALUES ('$post_id','$tags_id[$i]')";
                  $write->query($query);
                  
                  
              }catch(Exception $e)
              {
                  
              }    
        }
        
        
    }*/


    public function getTagsAjax($tag)
    {
        //$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        //$write->setFetchMode(Zend_Db::FETCH_OBJ);

        //$query = "select id,name from blog_tag where name like '%$tag%'";
        //print_r($write->fetchAll($query));exit;
        return $this->getCollection()->addFieldToFilter('name',array('like'=>"%$tag%"));
    }
}
