<?php

class EM_Blog_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'blog';
        $this->_controller = 'adminhtml_category';
        $id = Mage::app()->getFrontController()->getRequest()->getParam('id',0);
        $this->setValidationUrl(Mage::helper('adminhtml')->getUrl('blog/adminhtml_category/validate',array('id'=>$id)));
        $this->_updateButton('save', 'label', Mage::helper('blog')->__('Save Category'),'onclick','save');
        $this->_updateButton('delete', 'label', Mage::helper('blog')->__('Delete Category'));
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            }

            editForm._processValidationResult = function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.error){
                    if (response.attribute && $(response.attribute)) {
                        $(response.attribute).setHasError(true, editForm);
                        Validation.ajaxError($(response.attribute), response.message);
                        if (!Prototype.Browser.IE){
                            $(response.attribute).focus();
                        }
                    }
                    else if ($('messages')) {
                        if(!$('advice-validate-ajax-sku'))
                        {
                            var div = new Element('div', { 'class': 'validation-advice', id: 'advice-validate-ajax-sku' }).update(response.message);
                            $('url').value = response.identifier;
                            $('url').up().insert(div);
                        }
                    }
                }
                else{
                    editForm._submit();
                }
            };

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            /*Event.observe(window, 'load', function() {
                var tmp = $$('#blog_category_articles_table tbody tr td.a-center input');
                    if(tmp)
                    {
                        tmp.each(function(input){
                            input.writeAttribute('name','product_ids');
                        });
                        //$('in_category_products').value = value;
                    }

                
            });*/
            
            
            
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cat_data') && Mage::registry('cat_data')->getId() ) {
            return Mage::helper('blog')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('cat_data')->getCatName()));
        } else {
            return Mage::helper('blog')->__('Add New Category');
        }
    }
    
}