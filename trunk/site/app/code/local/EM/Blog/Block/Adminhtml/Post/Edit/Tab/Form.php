<?php

class EM_Blog_Block_Adminhtml_Post_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
	/**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            //$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('post_form', array('legend'=>Mage::helper('blog')->__('Post information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('blog')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		
      $fieldset->addField('post_identifier', 'text', array(
          'label'     => Mage::helper('blog')->__('Identifier'),
          'name'      => 'post_identifier',
          'class'     => 'validate-ajax'
      ));
      
      $postImage = $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('blog')->__('Image'),
          'name'      => 'image',
          'value'     => $this->getData('path').$this->getValue(),    
      ));

      if($pathImage = Mage::registry('post_data')->getImage())
      {
        $imageHtml = "<div><img src='".Mage::helper('blog')->resizeImage($pathImage,50,50,"em_blog/posts",'admin')."'/></div>";

        $postImage->setAfterElementHtml($imageHtml);
      }

       if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
      
      $fieldset->addField('post_by', 'select', array(
          'label'     => Mage::helper('blog')->__('Author'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'post_by',
          'values'    => Mage::getModel('blog/post')->getUserList(),    
      ));
      
      $fieldset->addField('post_on', 'date', array(
          'label'     => Mage::helper('blog')->__('Created Date'),
          //'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
          'format'    =>'yyyy-MM-dd',  
          'required'  => false,
          'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
          'name'      => 'post_on',
          //'values'    => Mage::getModel('blog/post')->getEmailList(),    
      ));
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('blog')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blog')->__('Enabled'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('Disabled'),
              ),
          ),
      ));
      $fieldset->addField('allow_comment', 'select', array(
          'label'     => Mage::helper('blog')->__('Allow Comment'),
          'name'      => 'allow_comment',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('Login User'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blog')->__('Everyone'),
              ),
              
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('blog')->__('Stop'),
              ),
          ),
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