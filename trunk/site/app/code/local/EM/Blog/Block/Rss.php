<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-L.txt
 *
 * @category   AW
 * @package    AW_Blog
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-L.txt
 */

class EM_Blog_Block_Rss extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
	    $this->setCacheKey('rss_catalog_category_'
            .$this->getRequest()->getParam('cid').'_'
            .$this->getRequest()->getParam('sid')
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $rssObj = Mage::getModel('rss/rss');

        $route = Mage::helper('blog')->getRoute();

        $url = $this->getUrl($route);
        $catId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('cat'));
        
        if($catId)
            $titleEnd = ' - '.Mage::getModel('blog/category')->load($catId)->getCatName();
        elseif($tagId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('tag_id')))
            $titleEnd = ' - tag '.Mage::getModel('blog/tag')->load($tagId)->getName();
        $title = Mage::getStoreConfig('blog/info/page_title').$titleEnd;
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $url,
                'charset'     => 'UTF-8'
                );

        if (Mage::getStoreConfig('blog/rss/image') != "")
        {
                $data['image'] = $this->getSkinUrl(Mage::getStoreConfig('blog/rss/rssimage'));
        }

        $rssObj->_addHeader($data);

        $collection = $this->_initListPost();

        //$collection->setPageSize(2);
        //$collection->setCurPage(1);


        $catId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('cat'));
		$helper = Mage::helper('cms');
		$processor = $helper->getBlockTemplateProcessor();

        if ($collection->getSize()>0) {
                foreach ($collection as $post) {

                        $data = array(
                                                'title'         => $post->getTitle(),
                                                'link'          => Mage::helper('blog')->getCatUrl($catId) . $post->getPostIdentifier().'.html',
                                                'description'   => $processor->filter($post->getPostContent())
                                                );

                        $rssObj->_addEntry($data);
                }
        }

        return $rssObj->createRssXml();
    }

    public function _initListPost()
    {
        $rs = array();
        $tagId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('tag_id'));
        $catId = Mage::helper('core')->urlDecode($this->getRequest()->getParam('cat'));

        if($tagId)
                $queryTag = 'btp.post_id=main_table.id
         AND btp.tag_id='.$tagId;
        else
                $queryTag = '1';

        $store_id = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('blog/post')->getCollection();
        $collection->distinct(true);
        $collection->getSelect()
            ->join(
                array('user_table'=>Mage::getSingleton('core/resource')->getTableName('admin_user')),
                'user_table.user_id=main_table.post_by AND main_table.status=1'
                 ,
                array('username'=>'username')
             );
        if($catId)
        {
            $collection->getSelect()
                        ->join(
                            array('cat_post'=>Mage::getSingleton('core/resource')->getTableName('blog_category_post')),
                            'cat_post.post_id=main_table.id',
                             array('post_id'=>'cat_post.post_id')
                       )->join(
                             array('cat'=>Mage::getSingleton('core/resource')->getTableName('blog_category')),
                             'cat_post.cat_id=cat.id
                              AND (cat.path like "%/'.$catId.'/%" OR cat.id='.$catId.')',
                             array('cat_post_id'=>'cat_post.post_id')
                       );
        }

        $collection->getSelect()->join(
                array('bps'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')),
                'bps.post_id=main_table.id
                 AND (bps.store_id='.$store_id.' OR bps.store_id=0)',
                array('post_store'=>'bps.store_id')
             );
        if($tagId)
        {
            $collection->getSelect()->join(
                array('post_tag'=>Mage::getSingleton('core/resource')->getTableName('blog_tag_post')),
                'post_tag.tag_id='.$tagId.'
                 AND post_tag.post_id=main_table.id',
                array('tag_id'=>'post_tag.tag_id')
             );
        }
        $collection->getSelect()->order( array('post_on DESC') );
    //echo (string)$collection->getSelect();exit;
        return $collection;
    }
}
