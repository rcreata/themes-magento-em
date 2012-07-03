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

class EM_Blog_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $urlRoot = Mage::app()->getRequest()->getPathInfo();
        if(strstr($urlRoot.'/',"/blog/"))
			$front->addRouter('blog', $this);
    }

    public function analyticUrl($url)
    {
         $contentUrl = explode("/",$url);
         if(count($contentUrl) == 2 && $contentUrl[0] == "tag")
             $condition = $url;
         else
             $condition = $contentUrl[count($contentUrl)-1];
  
         $urlData = Mage::getModel('blog/blogurl')->getCollection()
                            ->addFieldToFilter('request_path',$condition)
                            ->getFirstItem();
         return $urlData;
    }

    public function match(Zend_Controller_Request_Http $request)
    {
		if (!Mage::app()->isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlRoot = Mage::app()->getRequest()->getPathInfo();
        if(!strstr($urlRoot.'/',"/blog/"))
            return true;
		$route = Mage::helper('blog')->getRoute();
		$uri =  str_replace("/blog/","",strstr(Mage::app()->getRequest()->getPathInfo(),"/blog/"));

        if(trim($uri,"/") == "taglist")//go to page view all tag
        {
             Mage::app()->getRequest()->setControllerName('index');
             Mage::app()->getRequest()->setActionName('taglist');
             return true;
        }
        if($uri)
        {
              //Xu ly phan trang
              if($page = strstr($uri,"page/"))
              {
                    //$tmp = explode("/",substr($uri,strpos($uri,"/page/")+1),strlen($uri)-strpos($uri,"/page/")-1);
                    $tmp = explode("/", $page);
                    Mage::app()->getRequest()->setParam($tmp[0],$tmp[1]);
                    $uri = str_replace("/$page","",$uri);
                    //$uri = trim(substr($uri,0,strpos($uri,"/page/")));
                    //echo $requestInfo;exit;
              }

              $requestInfo = trim($uri);
              $tmp = explode("_", $requestInfo);
              if($tmp[0] == "adminhtml")
                  return true;
              $urlData = $this->analyticUrl($requestInfo);

              if($postId = $urlData->getPostId())//xem chi tiet bai post
              {
                    Mage::app()->getRequest()->setControllerName('index');
                    Mage::app()->getRequest()->setActionName('view');
                    Mage::app()->getRequest()->setParam('id',$postId);
                    $contentUrl = explode("/",$uri);
                    if(count($contentUrl)>1)
                        Mage::app()->getRequest()->setParam('cat_id',$this->analyticUrl($contentUrl[count($contentUrl)-2].'.html')->getCatId());
                    return true;
              }
              elseif($tagId = $urlData->getTagId())
              {
                    Mage::app()->getRequest()->setControllerName('index');
                    Mage::app()->getRequest()->setActionName('tag');
                    Mage::app()->getRequest()->setParam('tag_id',$tagId);
                    return true;
              }
              elseif($catId = $urlData->getCatId())
              {
                  Mage::app()->getRequest()->setControllerName('index');
                  Mage::app()->getRequest()->setActionName('cat');
                  Mage::app()->getRequest()->setParam('cat',$catId);
                  return true;
              }
              else
              {
                    Mage::app()->getRequest()->setControllerName('index');
                    Mage::app()->getRequest()->setActionName('index');
                    return true;
              }

        }
		
    }
}
