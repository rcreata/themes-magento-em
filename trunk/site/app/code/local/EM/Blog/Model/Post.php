<?php

class EM_Blog_Model_Post extends Mage_Core_Model_Abstract
{
    protected $maxQty = 0;
    protected $minQty = 0;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/post');
    }
    
    public function getPosts()
    {
      $collection = Mage::getResourceModel('blog/post_collection');
      //$collection->addAttributeToSelect('name');      
      $collection->getSelect()->join(array('customer' => 'customer_entity'), 'customer.entity_id = post_by', array(
                'email'             => 'email',
                ));
    	
    	
    }
    
    public function getCategoryIds()
    {
        return $this->getResource()->getCategoryIds($this);
    }
	
    public function getUserList()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->setFetchMode(Zend_Db::FETCH_OBJ);
        $query = 'select user_id,firstname,lastname,username from '.Mage::getSingleton('core/resource')->getTableName('admin_user');
        $emails = $write->fetchAll($query);
        //print_r($emails);exit;
        $data = array();
        foreach($emails as $e)
        {
            $data[] = array('label' => $e->username,'value' => $e->user_id);
        }
        return $data;
    }
    public function getPostList()
    {
        $posts = $this->getCollection()->addFieldToFilter('status',1);
        //print_r($emails);exit;
        $data = array();
        foreach($posts as $p)
        {
            $data[] = array('label' => $p->getTitle(),'value' => $p->getId());
        }
        return $data;
    } 
    
    
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
    
    public function getStoreOfPost($id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select c.name,c.store_id
                  from ".Mage::getSingleton('core/resource')->getTableName('blog_post_store')." AS b,
                        ".Mage::getSingleton('core/resource')->getTableName('core_store')." AS c
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
    }
    
    public function setPostStore($stores,$post_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('blog_post_store')." (post_id,store_id) VALUES ";
        try {
            $write->query("delete from ".Mage::getSingleton('core/resource')->getTableName('blog_post_store')." where post_id=$post_id");
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
    }
    
    public function setTag($tags_id,$post_id,$status,$tags_name)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "delete from ".Mage::getSingleton('core/resource')->getTableName('blog_tag_post')." where post_id=$post_id";
        $write->query($query);
        for($i = 0;$i<count($tags_id);$i++)
        {
              try{
                  if($tags_id[$i] == 0)//day la tab moi
                  {//echo $tags_name[$i];exit;
                        $data = array('name'=>$tags_name[$i],'status'=>$status,'tag_identifier'=>Mage::helper('blog/post')->friendlyURL($tags_name[$i]));
                        
                        $model = Mage::getModel('blog/tag');
                        $model->setData($data)->setId();
                        $tags_id[$i] = $model->save()->getId();
                        $dataBlogUrl = array('post_id' => '','tag_id'=>$tags_id[$i],'cat_id'=>'','request_path'=>'tag/'.$data['tag_identifier'].'.html');
                        Mage::helper('blog')->saveAndUpdateUrl($dataBlogUrl,$tags_id[$i],'tag');
                        //echo $tags_id[$i];
                        //$query = "INSERT INTO blog_tag (id,name,status) VALUES (null,'$tags_name[$i]','$status')";
                        //echo $tags_id[$i];exit;
                        //$write->query($query);
                        //$tmp = $write->fetchRow("select id from blog_tag where name='$tags_name[$i]' limit 1");
                        
                            
                  }  
                  
                  
                  
              }catch(Exception $e)
              {
                  
              }
              $query = "SET FOREIGN_KEY_CHECKS=0;";
                    $write->query($query);
                    //echo "INSERT INTO blog_tag_post (post_id,tag_id) VALUES ('".$post_id."','".$tags_id[$i]."')";exit;
              $query = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('blog_tag_post')." (post_id,tag_id) VALUES ('".$post_id."','".$tags_id[$i]."')";
              $write->query($query);    
        }
        
        
    }
    
    public function getTagsOfPost($post_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select t.name,t.id
                  from ".Mage::getSingleton('core/resource')->getTableName('blog_tag')." as t,
                       ".Mage::getSingleton('core/resource')->getTableName('blog_tag_post')." as tp
                  where t.id=tp.tag_id and tp.post_id=$post_id";
        return $write->fetchAll($query);
    }
	/*public function getRecent()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $store_id = Mage::app()->getStore()->getId();
        //->getStoreId();
        //echo $store_id;exit;
        $query = "select bp.* from blog_post as bp,blog_post_store as bps 
                  where status = 1 
                        AND bps.post_id=bp.id
                        AND bps.store_id=$store_id
                        order by post_on desc limit 5";
        return $write->fetchAll($query);
    }*/
    
    public function setMaxQty($qty)
    {
        $this->maxQty = $qty;
        return $this;
    }
    
    public function setMinQty($qty)
    {
        $this->minQty = $qty;
        return $this;
    }
    
    public function getMaxQty()//lay ra so luong bai post cua tag co nhieu bai post nhat
    {
        return $this->maxQty;
    }
    
    public function getMinQty()//lay ra so luong bai post cua tag co it bai viet nhat
    {
        return $this->minQty;
    }
    
    public function getTagCloud()
    {
         $write = Mage::getSingleton('core/resource')->getConnection('core_write');
         $query = "select b.id,name,count(bp.post_id) as qty,b.tag_identifier as url
                   from ".Mage::getSingleton('core/resource')->getTableName('blog_tag')." AS b,
                        ".Mage::getSingleton('core/resource')->getTableName('blog_tag_post')." as bp
                   where b.id=bp.tag_id and b.status=0
                   group by b.id
                   order by count(bp.post_id) desc";
         $tmp = $write->fetchAll($query);
         if(count($tmp))
         {
            $this->setMaxQty($tmp[0]['qty']);
            $this->setMinQty($tmp[count($tmp)-1]['qty']);
         }
         return $tmp;
         
    }

    public function setCatsOfPost($cats = array(),$postId)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "delete from ".Mage::getSingleton('core/resource')->getTableName('blog_category_post')." where post_id=".$postId;
        $write->query($query);

        if(count($cats))
        {
            $query = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('blog_category_post')." (post_id,cat_id) VALUES ";
            
            foreach($cats as $c)
            {
                $query .= "($postId,$c),";
            }
            $query = substr($query,0,strlen($query)-1);
            $write->query($query);
        }
    }

}