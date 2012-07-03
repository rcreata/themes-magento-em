<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product in category grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class EM_Blog_Block_Adminhtml_Category_Edit_Tab_Articles extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('blog_category_articles');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setTemplate('em_blog/category/edit/grid.phtml');
    }

    public function getCategory()
    {
        return Mage::registry('cat_data');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedPosts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_category'=>1));
        }
        //$catId = $this->getCategory()->getId();
        $collection = Mage::getModel('blog/post')->getCollection();
        /*$filter = $this->getRequest()->getParam('in_category');
        if(!isset($filter))
               $filter = "1";
       //$filter = 0;
        if($filter == "1")
        {
            $collection->getSelect()
                       ->join(array('cat_post'=>Mage::getSingleton('core/resource')->getTableName('blog_category_post')), 'cat_post.post_id = main_table.id and cat_post.cat_id='.$catId,array('cat_id'=>'cat_post.cat_id'))
                       ->join(array('cat'=>Mage::getSingleton('core/resource')->getTableName('blog_category')),'cat.id=cat_post.cat_id',array('cat_name'=>'cat.cat_name'));
    
        }
        elseif($filter == "0")
        {echo '123';exit;
            $collection->getSelect()->where("main_table.id not in (select post_id from ".Mage::getSingleton('core/resource')->getTableName('blog_category_post')." where cat_id=$catId)");
echo (string)$collection->getSelect();exit;
  //          echo 'abc';exit;
        }
        else
            $collection->getSelect();*/
        //echo (string)$collection->getSelect();exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if (!$this->getCategory()->getPostReadonly()) {
            $this->addColumn('in_category', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_category',
                'values'    => $this->_getSelectedPosts(),
                'align'     => 'center',
                'index'     => 'id'
            ));
        }
        $this->addColumn('id', array(
            'header'    => Mage::helper('blog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'id'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('blog')->__('Name'),
            'index'     => 'title'
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _getSelectedPosts()
    {
        $posts = $this->getRequest()->getPost('selected_posts');
        if (is_null($posts)) {
            $posts = $this->getCategory()->getPostPosition();
            return array_keys($posts);
        }
        return $posts;
    }

}

