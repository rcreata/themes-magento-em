<?php

class EM_Blog_Model_Config_Recentpost
{
	protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options =  array(
            array('value'=>5,'label'=> Mage::helper('blog')->__('5')),
            array('value'=>10,'label'=> Mage::helper('blog')->__('10')),
            array('value'=>15,'label'=> Mage::helper('blog')->__('15')),
        );
        }
        return $this->_options;
    }
}
