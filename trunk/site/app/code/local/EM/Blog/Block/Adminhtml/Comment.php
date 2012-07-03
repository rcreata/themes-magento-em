<?php
class EM_Blog_Block_Adminhtml_Comment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	
    $this->_controller = 'adminhtml_comment';
    $this->_blockGroup = 'blog';
    $this->_headerText = Mage::helper('blog')->__('Comments Manager');
    $this->_addButtonLabel = Mage::helper('blog')->__('Add New Comment');
    //exit('abc');
    parent::__construct();
  }
}