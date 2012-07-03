<?php
class EM_Blog_Block_Post extends Mage_Core_Block_Template
{
    protected $sum = 0;
    protected $posts;
    protected function _prepareLayout()
    {
        $route = Mage::helper('blog')->getRoute();
        $isBlogPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'blog';

        // show breadcrumbs
        if ($isBlogPage && Mage::getStoreConfig('blog/info/blogcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
		
                        $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('blog')->__('Home'), 'title'=>Mage::helper('blog')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));;
                if($tag = @urldecode($this->getRequest()->getParam('tag_id'))){//tag page
                    $name = Mage::getModel('blog/tag')->load($tag)->getName();
                    $breadcrumbs->addCrumb('blog', array('label'=>Mage::getStoreConfig('blog/info/title'), 'title'=>Mage::helper('blog')->__('Return to ' .Mage::getStoreConfig('blog/info/title')), 'link'=>Mage::getUrl($route)));
                    $breadcrumbs->addCrumb('blog_tag', array('label'=>Mage::helper('blog')->__('Tagged with "%s"', $name), 'title'=> Mage::helper('blog')->__('Tagged with "%s"', $name) ));
                }
				else if($this->getRequest()->getParam('id') || $this->getRequest()->getParam('cat'))
					$breadcrumbs->addCrumb('blog', array('label'=>Mage::getStoreConfig('blog/info/title'), 'title'=>Mage::helper('blog')->__('Return to ' .Mage::getStoreConfig('blog/info/title')), 'link'=>Mage::getUrl($route)));
				else{
                    $breadcrumbs->addCrumb('blog', array('label'=>Mage::getStoreConfig('blog/info/title'), 'title'=>Mage::helper('blog')->__('Return to ' .Mage::getStoreConfig('blog/info/title'))));
                }
        }



        //init toolbar
        $toolbar = $this->getLayout()->createBlock('blog/toolbar', 'Toolbar');
        $toolbar->setTemplate('em_blog/toolbar.phtml');
        $toolbar->setOrderField('title');
        if(!$this->getRequest()->getParam('limit')){
            $limit = Mage::getStoreConfig('blog/info/pagesize');
            /*$_limit = 0;
            foreach($toolbar->getAvailableLimit() as $value){
                    if($limit < $value) break;
                    $_limit = $value;
            }*/

            Mage::getSingleton('catalog/session')->setLimitPage($_limit);
            $this->getRequest()->setParam('limit',$_limit);
        }
        $toolbar->setChild('em_blog_pager',$this->getLayout()->createBlock('blog/toolbar_pager','em_blog_pager'));
        $toolbar->disableExpanded();

    	$this->setToolbar($toolbar);
        
        return parent::_prepareLayout();

    }
	

    public function getCurrentUrl()
    {
        $route = Mage::getUrl();
        $uri =  Mage::app()->getRequest()->getPathInfo();
        $uri = substr($uri,1,strlen($uri)-1);
        return $route.$uri;
    }

    public function _initListPost()
    {
        $rs = array();
        $tagId = (int)$this->getRequest()->getParam('tag_id');
        $catId = (int)$this->getRequest()->getParam('cat');
        
    

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

    public function getPosts()
    {
        $collection = $this->_initListPost();
        $collection->setPageSize(Mage::getStoreConfig('blog/info/pagesize'))
              ->setCurPage($this->getRequest()->getParam('page',1));
        
        $this->setCollection($collection);
        $this->setSize($collection->count());
        
        $route = Mage::helper('blog')->getRoute();
        return $collection;
    }

    public function getCommentByPost($postId)
    {
        return Mage::getModel('blog/comment')->getCollection()
                ->addFieldTofilter('post_id',$postId)
                ->addFieldToFilter('status_comment',array('gt'=>1-Mage::getStoreConfig('blog/info/show_comment_pending')));
    }
   
}
