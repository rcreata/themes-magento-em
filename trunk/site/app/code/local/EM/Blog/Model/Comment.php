<?php

class EM_Blog_Model_Comment extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blog/comment');
    }
    
    /*public function getEmailList()
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->setFetchMode(Zend_Db::FETCH_OBJ);
        $query = 'select entity_id,email from customer_entity';
        $emails = $write->fetchAll($query);
        //print_r($emails);exit;
        $data = array();
        foreach($emails as $e)
        {
            $data[] = array('label' => $e->email,'value' => $e->entity_id);
        }
        return $data;
    }*/
}