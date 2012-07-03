<?php

class EM_Blog_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cat_form', array('legend'=>Mage::helper('blog')->__('Category information')));
     
      $fieldset->addField('cat_name', 'text', array(
          'label'     => Mage::helper('blog')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'cat_name',
      ));
		
      $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('blog')->__('Description'),
            'title' => Mage::helper('blog')->__('Meta Description'),
            'style'     => 'height:15em;width:25',
        ));
      $cat = $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('blog')->__('Image'),
          'name'      => 'image',
          'value'     => $this->getData('path').$this->getValue(),    
      ));

      if($pathImage = Mage::registry('cat_data')->getImage())
      {
        $imageHtml = "<div><img src='".Mage::helper('blog')->resizeImage($pathImage,50,50,"em_blog/category",'admin')."'/></div>";
      
        $cat->setAfterElementHtml($imageHtml);
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

      $fieldset->addField('page_title', 'text', array(
          'label'     => Mage::helper('blog')->__('Page Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'page_title',
      ));
      
      $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('blog')->__('Keywords'),
            'title' => Mage::helper('blog')->__('Meta Keywords'),
            'style'     => 'height:10em;width:25',
      ));

      $fieldset->addField('meta_description', 'textarea', array(
            'name' => 'meta_description',
            'label' => Mage::helper('blog')->__('Meta Description'),
            'title' => Mage::helper('blog')->__('Meta Description'),
            'style'     => 'height:10em;width:25',
      ));
      
      $fieldset->addField('is_active', 'select', array(
          'label'     => Mage::helper('blog')->__('Is Active'),
          'name'      => 'is_active',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blog')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('No'),
              ),
          ),
      ));

      $fieldset->addField('parent_id', 'select', array(
          'label'     => Mage::helper('blog')->__('Parent Category'),
          'name'      => 'parent_id',
          'required'  => true,
          'class'     => 'required-entry',
          'values'    => Mage::helper('blog')->getCategories(Mage::app()->getFrontController()->getRequest()->getParam('id'))
      ));
      
      $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('blog')->__('Url'),
          'name'      => 'url',
      ));

      $fieldset->addField('show_image', 'select', array(
          'label'     => Mage::helper('blog')->__('Show image at frontend'),
          'name'      => 'show_image',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('blog')->__('Yes'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('blog')->__('No'),
              ),
          ),
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