<?php

class EM_Blog_Model_Mysql4_Post_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/post');
        
    }

    /*public function getCurPage($displacement = 0)
    {
        return $this->_curPage;
    }*/

    /*public function getSize()
    {
        return count($this->getData());
    }*/
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(distinct main_table.id)');
   
        return $countSelect;
    }

    public function addWebsiteNamesToResult()
    {
        $productStores = array();
        foreach ($this as $product) {
            $productWebsites[$product->getId()] = array();
        }

        if (!empty($productWebsites)) {
            $select = $this->getConnection()->select()
                ->from(array('post_store'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')))
                ->join(
                        array('store'=>Mage::getSingleton('core/resource')->getTableName('core_store')),
                        'post_store.store_id=store.store_id',
                        array('id_store'=>'store.store_id')
                        )
                ->join(
                    array('website'=>$this->getResource()->getTable('core/website')),
                    'website.website_id=store.website_id',
                    array('website_id'));
                

            $data = $this->getConnection()->fetchAll($select);
          
            foreach ($data as $row) {
                if(!in_array($row['website_id'],  $productWebsites[$row['post_id']]))
                    $productWebsites[$row['post_id']][] = $row['website_id'];
            }
        }

        foreach ($this as $product) {
            if (isset($productWebsites[$product->getId()])) {
                $product->setData('website_id', $productWebsites[$product->getId()]);
            }
        }
        return $this;
    }
}