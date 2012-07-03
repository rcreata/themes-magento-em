<?php

class EM_Blog_Block_Adminhtml_Category_Edit_Tab_Display extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cat_form', array('legend'=>Mage::helper('blog')->__('Display Setting')));
     
      $fieldset->addField('display_mode', 'select', array(
          'label'     => Mage::helper('blog')->__('Display Mode'),
          'name'      => 'display_mode',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('Articles Only'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blog')->__('Static block only'),
              ),
              
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('blog')->__('Articles and Static block'),
              ),
          ),
      ));
      
      $fieldset->addField('cms_block', 'select', array(
          'label'     => Mage::helper('blog')->__('CMS Block'),
          'name'      => 'cms_block',
          /*'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('Please select a static block ...'),
              ),

              array(
                  'value'     => Mage::helper('blog')->getStaticBlocks(),
                  'label'     => Mage::helper('blog')->__('(Similar product category)'),
              ),
          ),*/
          'values'  =>  Mage::helper('blog')->getStaticBlocks()
      ));
       
      if ( Mage::getSingleton('adminhtml/session')->getCatData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCatData());
          Mage::getSingleton('adminhtml/session')->setCatData(null);
      } elseif ( Mage::registry('cat_data') ) {
          $form->setValues(Mage::registry('cat_data')->getData());
      }
      return parent::_prepareForm();
  }
}