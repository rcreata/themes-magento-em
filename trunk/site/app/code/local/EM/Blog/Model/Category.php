<?php
class EM_Blog_Model_Category extends Mage_Core_Model_Abstract
{
    protected $checked = false;
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/category');
    }

    protected function setChecked($bool)
    {
        $this->checked = $bool;
    }

    protected function getChecked()
    {
        return $this->checked;
    }

    public function getPostPosition()
    {
        if (!$this->getId()) {
            return array();
        }

        $array = $this->getData('post_position');
        if (is_null($array)) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('post_position', $array);
        }
        return $array;
    }

    public function setPostsOfCat($posts = array(),$category)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "delete from ".Mage::getSingleton('core/resource')->getTableName('blog_category_post')." where cat_id=".$category->getId();
        $write->query($query);

        if(count($posts))
        {
            $query = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('blog_category_post')." (post_id,cat_id) VALUES ";
            $id = $category->getId();
            foreach($posts as $p)
            {
                $query .= "($p,$id),";
            }
            $query = substr($query,0,strlen($query)-1);
            $write->query($query);
        }
    }

    public function getStoreOfCat($id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "select c.store_id
                  from ".Mage::getSingleton('core/resource')->getTableName('blog_cat_store')." as b,
                       ".Mage::getSingleton('core/resource')->getTableName('core_store')." as c
                  where b.store_id=c.store_id and b.cat_id=$id";
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

    public function setCatStore($stores,$cat_id)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "INSERT INTO ".Mage::getSingleton('core/resource')->getTableName('blog_cat_store')." (cat_id,store_id) VALUES ";
        try {
            $write->query("delete from ".Mage::getSingleton('core/resource')->getTableName('blog_cat_store')." where cat_id=$cat_id");
        }
        catch(Exception $e)
        {

        }
        //print_r($stores);exit;
        if(count($stores))
        {
            foreach($stores as $s)
            {
                $query .= "($cat_id,$s),";
            }
            $query = substr($query,0,strlen($query)-1);
            $write->query($query);
        }
        //INSERT INTO `magento_3`.`blog_post_store` (`post_id`, `store_id`) VALUES ('1', '2'), ('1', '3');
    }

    public function hasChildren()
    {
        return $this->getResource()->getChildrenAmount($this);

    }

    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    public function validate($identifier,$id)
    {
        $collection = $this->getCollection()->addFieldToFilter('url',$identifier);
        if($id)
            $collection->addFieldToFilter('id',array('neq'=>$id));
        return $collection->count() > 0;
    }
    
}