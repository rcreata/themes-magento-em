<?php
class EM_Blog_Block_Post_View extends Mage_Core_Block_Template
{
    protected $sum = 0;
    protected $posts;
    protected $allowComment = 0;
    protected $nextPost = null;
    protected $prevPost = null;
    protected $linkCat = "";
    protected function _prepareLayout()
    {
        $route = Mage::helper('blog')->getRoute();
        $isBlogPage = Mage::app()->getFrontController()->getAction()->getRequest()->getModuleName() == 'blog';

        // show breadcrumbs
        if ($isBlogPage && Mage::getStoreConfig('blog/info/blogcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))){
            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('blog')->__('Home'), 'title'=>Mage::helper('blog')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('blog', array('label'=>Mage::helper('blog')->__('Blog'), 'title'=>Mage::helper('blog')->__('Go to Blog'), 'link'=>Mage::getUrl('blog')));

            $catId = $this->getRequest()->getParam('cat_id');
            $postId = $this->getRequest()->getParam('id');
            $post = Mage::getModel('blog/post')->load($postId);
            if($catId){
                $catNames = array();
                $category = Mage::getModel('blog/category')->load($catId);
                while($category->getId() != Mage::getStoreConfig('blog/info/root_category_id'))
                {
                    $catNames[] = $category;
                    $category = Mage::getModel('blog/category')->load($category->getParentId());
                }
                $catNames = array_reverse($catNames);
                $link = "";
                foreach($catNames as $c)
                {
                    $breadcrumbs->addCrumb('blog_cat_'.$c->getId(), array(
                                            'label'=>$c->getCatName(),
                                            'title'=> $c->getCatName(),
                                            'link'=>Mage::getUrl('blog').$link.$c->getUrl().'.html'));
                    $link .= $c->getUrl().'/';
                }
                $this->setLinkCat($link);
            }
            
            $breadcrumbs->addCrumb('post', array('label'=>$post->getTitle(), 'title'=>$post->getTitle()));
        }
     
        return parent::_prepareLayout();

    }

    public function setLinkCat($link)
    {
        $this->linkCat = $link;
    }

    public function getLinkCat()
    {
        return $this->linkCat;
    }

    public function setAllowComment($allow)
    {
        $this->allowComment = $allow;
        return $this;
    }

    public function getAllowComment()
    {
        return $this->allowComment;
    }

    public function setNextPost($post)
    {
        $this->nextPost = $post;
    }

    public function setPrevPost($post)
    {
        $this->prevPost = $post;
    }

    public function getNextPost()
    {
        return $this->nextPost;
    }

    public function getPrevPost()
    {
        return $this->prevPost;
    }

    public function getPost()
    {
        return Mage::registry('post_detail');
    }

    public function getPostById()
    {
        $id = $this->getRequest()->getParam('id');
        $catId = $this->getRequest()->getParam('cat_id',0);
        $cur = Mage::registry('post_detail');
        if(!$cur || $cur['id']!=$id)
        {
            $listNextPre = Mage::registry('list_next_pre');
            
            if($listNextPre && $listNextPre->catId == $catId)
                $collection = $listNextPre->collection;
            else
            {
                $collection = Mage::getModel('blog/post')->getCollection();
                $collection->addFieldToFilter('status',1);
                $collection->setOrder('post_on','desc');
                $store_id = Mage::app()->getStore()->getId();

                if($catId)
                {
                    $collection->distinct(true);
                    $collection->getSelect()
                            ->join(
                                    array('post_cat'=>Mage::getSingleton('core/resource')->getTableName('blog_category_post')),
                                    'main_table.id=post_cat.post_id',
                                    array('')
                           )->join(
                                    array('cat'=>Mage::getSingleton('core/resource')->getTableName('blog_category')),
                                    "post_cat.cat_id=cat.id
                                     AND (cat.path like '%/$catId/%' OR cat.id=$catId)",
                                    array('')
                           );
                }
                $collection->getSelect()->join(
                                    array('bps'=>Mage::getSingleton('core/resource')->getTableName('blog_post_store')),
                                    'bps.post_id=main_table.id
                                     AND (bps.store_id='.$store_id.' OR bps.store_id=0)',
                                    array('')
                           );
                
                $collection->getSelect()->order( array('post_on DESC') );
                $listNextPre = new stdClass();
                $listNextPre->catId = $catId;
                $listNextPre->collection = $collection;
                Mage::register('list_next_pre', $listNextPre);
                
            }

            ////////////Get previous and next post//////////////

            $i = 0;
            $tmp = array();
            foreach($collection as $key => $l)
            {
                $tmp[] = $l;
                if($hasNext)
                {
                    $this->setNextPost($tmp[$i]);
                    break;
                }
                if($l->getId() == $id)
                {
                    if($i > 0){
                        $this->setPrevPost($tmp[$i-1]);}
                    if($i < $collection->count()-1)
                        $hasNext = 1;
                    
                }
                $i++;
            }
            ///////////End Get previous and next post///////////

            $collection->getSelect()
                ->join(
                    array('user_post_table'=>Mage::getSingleton('core/resource')->getTableName('admin_user')),
                    'user_post_table.user_id=main_table.post_by AND main_table.id='.$id,
                    array('username'=>'username','email'=>'email')
                 );
            //$post = $collection->getFirstItem();
           
            $tmp = $collection->getData();
            $post = $tmp[0];
           
            Mage::unregister('post_detail');
            Mage::register('post_detail',  $post);

            $allow_comment = (int)$post['allow_comment'];
            if($allow_comment == 0)//chi co user dang nhap moi duoc comment
            {
                if($this->helper('customer')->isLoggedIn())
                    $this->setAllowComment(1);//duoc comment
                else
                    $this->setAllowComment(0);//khong duoc comment
            }
            elseif($allow_comment == 1)//Ai cung duoc comment
                $this->setAllowComment(1);//duoc comment
            else//Ai cung khong duoc comment
                $this->setAllowComment(0);//khong duoc comment
           

        }
       return Mage::registry('post_detail');

    }

    


}
