<?php

class EM_Blog_Block_Adminhtml_Post_Edit_Tab_Description extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('blog')->__('Meta Information')));
     
      $fieldset->addField('post_meta_keywords', 'textarea', array(
            'name' => 'post_meta_keywords',
            'label' => Mage::helper('blog')->__('Keywords'),
            'title' => Mage::helper('blog')->__('Meta Keywords'),
            'style'     => 'height:10em;width:25',
        ));
        
        $fieldset->addField('post_meta_description', 'textarea', array(
            'name' => 'post_meta_description',
            'label' => Mage::helper('blog')->__('Description'),
            'title' => Mage::helper('blog')->__('Meta Description'),
            'style'     => 'height:15em;width:25',
        ));
      
      if ( Mage::getSingleton('adminhtml/session')->getPostData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
          Mage::getSingleton('adminhtml/session')->setPostData(null);
      } elseif ( Mage::registry('post_data') ) {
          $form->setValues(Mage::registry('post_data')->getData());
      }
      return parent::_prepareForm();
  }
}