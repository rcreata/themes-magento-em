<?php

class EM_Blog_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('categoryGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      if($curStore = Mage::app()->getFrontController()->getRequest()->getParam('store'))
          $stores .= "AND (cat_store.store_id = 0 OR cat_store.store_id = $curStore)";
      $collection = Mage::getResourceModel('blog/category_collection');
      $collection->distinct(true);
      //$collection->addAttributeToSelect('name');
      $collection->getSelect()->join(array('cat_store' => Mage::getSingleton('core/resource')->getTableName('blog_cat_store')),
              'cat_store.cat_id = main_table.id
              '.$stores.'
              AND main_table.level > 1    ', array(
                'cat_id'             => 'cat_store.cat_id',
                ));
      
      //echo (string) $collection->getSelect();exit;
                
      $this->setCollection($collection);
     
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('blog')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('cat_name', array(
          'header'    => Mage::helper('blog')->__('Category Name'),
          'align'     =>'left',
          'index'     => 'cat_name',
      ));
      
      $this->addColumn('is_active', array(
          'header'    => Mage::helper('blog')->__('Status'),
          'align'     =>'left',
          'type'      =>'options',  
          'index'     =>'is_active',
          'options'   =>array(1 => 'Enable',0 => 'Disable'),
      ));
     
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('blog')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
					array(
                        'caption'   => Mage::helper('blog')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
                        'confirm'  => Mage::helper('blog')->__('Are you sure?')
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

		
		//$this->addExportType('*/*/exportCsv', Mage::helper('blog')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('blog')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('cat');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('blog')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('blog')->__('Are you sure?')
        ));

        //$statuses = Mage::getSingleton('blog/status')->getOptionArray();
        //$statuses = array(0=>'disable',1=>'Enable');
        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $statuses = array(
                        array('label'=>'Disabled', 'value'=>'0'),
                        array('label'=>'Enable', 'value'=>'1'),
        );  
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('blog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('blog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
  	
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}