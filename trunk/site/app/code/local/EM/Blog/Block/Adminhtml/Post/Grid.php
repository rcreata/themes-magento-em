<?php

class EM_Blog_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('postGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getResourceModel('blog/post_collection');
      $collection->distinct(true);
      $collection->getSelect()->join(array('user_post' => Mage::getSingleton('core/resource')->getTableName('admin_user')), 'user_post.user_id = post_by', array(
                'username'             => 'username',
                ));
      if($curStore = Mage::app()->getFrontController()->getRequest()->getParam('store'))
          $stores .= "AND (post_store.store_id = 0 OR post_store.store_id = $curStore)";
      $collection->getSelect()
              ->join(
                  array('post_store'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')),
                   'post_store.post_id = main_table.id '.$stores,
                  array('id_post'=>'post_store.post_id')
             )->join(
                  array('store'=>Mage::getSingleton('core/resource')->getTableName('core_store')),
                  'store.store_id = post_store.store_id',
                  array()
             );
     //echo (string)$collection->getSelect();exit;
      $this->setCollection($collection);
     
      parent::_prepareCollection();
      $this->getCollection()->addWebsiteNamesToResult();
      return $this;
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('blog')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('blog')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      /*$this->addColumn('post_content', array(
          'header'    => Mage::helper('blog')->__('Content'),
          'align'     =>'left',
          'index'     => 'post_content',
      ));*/
      
      $this->addColumn('post_identifier', array(
          'header'    => Mage::helper('blog')->__('Identifier'),
          'align'     =>'left',
          'index'     => 'post_identifier',
      ));

      $this->addColumn('image',
            array(
                'header'=> Mage::helper('catalog')->__('Image'),
                'width' => '75px',
                'index' => 'image',
                'filter'    => false,
                'sortable'  => false,
                'renderer'  => 'blog/renderer_image',
        ));

      $this->addColumn('username', array(
          'header'    => Mage::helper('blog')->__('Author'),
          'align'     =>'left',
          'index'     => 'username',
      ));
       $this->addColumn('post_on', array(
          'header'    => Mage::helper('blog')->__('Created Date'),
          'align'     =>'left',
          'index'     =>'post_on',
          'type'      =>'date',    
      ));
      
       $this->addColumn('status', array(
          'header'    => Mage::helper('blog')->__('Status'),
          'align'     =>'left',
          'type'      =>'options',  
          'index'     =>'status',
          'options'   =>array(1 => 'Enable',0 => 'disable'),  
      ));
       $this->addColumn('allow_comment', array(
          'header'    => Mage::helper('blog')->__('Allow Comment'),
          'align'     =>'left',
          'index'     =>'allow_comment',
          'type'      =>'options',
          'options'   =>array(0 => 'only login user',1 => 'every one',2 => 'stop'),        
      ));
       if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'website_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }
      

		
		$this->addExportType('*/*/exportCsv', Mage::helper('blog')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('blog')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('post');

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