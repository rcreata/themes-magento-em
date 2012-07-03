<?php

class EM_Blog_Block_Adminhtml_Post_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_post;
	
	public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function getPost()
    {
        return Mage::registry('post_data');
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setDataObject($this->getPost());

        $designFieldset = $form->addFieldset('design_fieldset', array(
            'legend' => Mage::helper('cms')->__('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );
        
        $designFieldset->addField('custom_design', 'select', array(
            'name'      => 'custom_design',
            'label'     => Mage::helper('cms')->__('Custom Design'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_design_from', 'date', array(
            'name'      => 'custom_design_from',
            'label'     => Mage::helper('cms')->__('Custom Design From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>'yyyy-MM-dd',
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_design_to', 'date', array(
            'name'      => 'custom_design_to',
            'label'     => Mage::helper('cms')->__('Custom Design To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    =>'yyyy-MM-dd',
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_layout', 'select', array(
            'name'      => 'custom_layout',
            'label'     => Mage::helper('cms')->__('Custom Layout'),
            'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
            'name'      => 'custom_layout_update_xml',
            'label'     => Mage::helper('cms')->__('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            //'disabled'  => $isElementDisabled
        ));
        
        if ( Mage::getSingleton('adminhtml/session')->getPostData() )
		{
		  $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
		  Mage::getSingleton('adminhtml/session')->setPostData(null);
		} elseif ( Mage::registry('post_data') ) {
		  $form->setValues(Mage::registry('post_data')->getData());
		}
        
        $this->setForm($form);
    }
}
