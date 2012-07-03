<?php

class EM_Blog_Block_Adminhtml_Post_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
  
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('blog')->__('Content'),'class'=>'fieldset-wide'));
     
      $fieldset->addField('post_content_heading', 'text', array(
          'label'     => Mage::helper('blog')->__('Content heading'),
          'class'     => 'required-entry',
          'required'  => true,
          //'rows'      => '1',
          //'style'     => 'height:5em;',
          'name'      => 'post_content_heading',
      ));

 
      /*$wysiwygConfig = Mage::getSingleton('blog/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );*/

       $fieldset->addField('post_intro', 'editor', array(
          'name'      => 'post_intro',
          'label'     => Mage::helper('blog')->__('Introduction'),
          'title'     => Mage::helper('blog')->__('Introduction'),
          'style'     => 'height:36em',
          'wysiwyg'   => true,
          'config'    => Mage::helper('blog')->editWysiwyg(),
          'disabled'  => false,
          'theme' => 'advanced',
          'required'  => true,
          //'AfterElementHtml' => 'abc',
      ));

      //print_r($wysiwygConfig);exit;
      $fieldset->addField('post_content', 'editor', array(
          'name'      => 'post_content',
          'label'     => Mage::helper('blog')->__('Content'),
          'title'     => Mage::helper('blog')->__('Content'),
          'style'     => 'height:36em',
          'wysiwyg'   => true,
          'config'    => Mage::helper('blog')->editWysiwyg(),
          'disabled'  => false,
          'theme' => 'advanced',    
          'required'  => true,
          //'AfterElementHtml' => 'abc',
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPostData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
          Mage::getSingleton('adminhtml/session')->setPostData(null);
      } elseif ( Mage::registry('post_data') ) {
          $form->setValues(Mage::registry('post_data')->getData());
      }
      //Mage::dispatchEvent('blog_adminhtml_post_edit_tab_content_prepare_form', array('form' => $form));
      return parent::_prepareForm();
  }
}