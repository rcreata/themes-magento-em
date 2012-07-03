<?php

class EM_Blog_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('category_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('blog')->__('Category Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('general', array(
          'label'     => Mage::helper('blog')->__('General Information'),
          'title'     => Mage::helper('blog')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_category_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('display_setting', array(
          'label'     => Mage::helper('blog')->__('Display Setting'),
          'title'     => Mage::helper('blog')->__('Display Setting'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_category_edit_tab_display')->toHtml(),
      ));

      $this->addTab('custom_design', array(
          'label'     => Mage::helper('blog')->__('Custom Design'),
          'title'     => Mage::helper('blog')->__('Custom Design'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_category_edit_tab_design')->toHtml(),
      ));
	  
      $this->addTab('articles', array(
          'label'     => Mage::helper('blog')->__('Articles'),
          'title'     => Mage::helper('blog')->__('Articles'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_category_edit_tab_articles')->toHtml(),
      ));

      /*$this->addTab('parent_id', array(
          'label'     => Mage::helper('blog')->__('Categories'),
          'url'       => $this->getUrl('blog/adminhtml_chooser', array('_current' => true)),
          'class'     => 'ajax',
      ));
      /*$this->addTab('tag', array(
          'label'     => Mage::helper('blog')->__('Tags'),
          'title'     => Mage::helper('blog')->__('Tags'),
          'content'   => $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_form')->toHtml(),
      ));*/
     
      return parent::_beforeToHtml();
  }
}