<?php
class EM_Blog_Adminhtml_PostController extends Mage_Adminhtml_Controller_Action
{
    public function _initPost()
    {
        $id = $this->getRequest()->getParam('id');
        if(!Mage::registry('post_data') || Mage::registry('post_data')->getId()!=$id)
        {
            $post = Mage::getModel('blog/post')->load($id);
            if(Mage::registry('post_data'))
                Mage::unregister ('post_data');
            Mage::register('post_data', $post);
        }
        return Mage::registry('post_data');
    }

    protected function _initAction() {
            $this->loadLayout()
                    ->_setActiveMenu('blog/items')
                    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

            return $this;
    }

    public function indexAction() {
            $this->_initAction();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Posts'));
            $this->renderLayout();
    }

    public function categoriesAction()
    {
        $this->_initPost();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function categoriesJsonAction()
    {
        $product = $this->_initPost();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blog/adminhtml_post_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $modelPost  = Mage::getModel('blog/post');
        $model = $modelPost->load($id);
        if($id)
        {
            $modelPost->setData('store_id',$modelPost->getStoreOfPost($id));
            $modelPost->setOrigData('store_id',$modelPost->getStoreOfPost($id));

            $modelPost->setData('tags',$modelPost->getTagsOfPost($id));
            $modelPost->setOrigData('tags',$modelPost->getTagsOfPost($id));
        }

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            
            if (!empty($data)) {
              //echo 'abc';exit;
                    $model->setData($data);
            }

            Mage::register('post_data', $model);
//print_r(Mage::registry('post_data')->getId());exit;
            $this->loadLayout();
            $this->_setActiveMenu('blog/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_post_edit'))
                    ->_addLeft($this->getLayout()->createBlock('blog/adminhtml_post_edit_tabs'));

            $this->renderLayout();
                    //echo 'abc';exit;
        } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Item does not exist'));
                $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        Mage::register('post_data', Mage::getModel('blog/post'));

        $this->loadLayout();
        $this->_setActiveMenu('blog/items');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_post_edit'))
                ->_addLeft($this->getLayout()->createBlock('blog/adminhtml_post_edit_tabs'));

        $this->renderLayout();
    }

    public function autotagAction()
    {
        $model = Mage::getModel('blog/tag');
        $tag = $this->_request->getParam('q');
        //header('Content-type: application/json');
        //echo json_encode($model->getTags($tag));
        $tagList = $model->getTagsAjax($tag);
        echo "<ul>";
        foreach($tagList as $t)
        {
           echo "<li tag='".$t->getName()."' value='".$t->getId()."'><strong>".$t->getName()."</strong></li>";
        }
        echo "</ul>";
        exit;
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/post');

            if(!$data['custom_design_from'])
                $data['custom_design_from'] = NULL;
            if(!$data['custom_design_to'])
                $data['custom_design_to'] = NULL;

            if(!$data['post_identifier'])
                 $data['post_identifier'] = Mage::helper('blog/post')->friendlyURL($data['title']);
            else
                 $data['post_identifier'] = Mage::helper('blog/post')->friendlyURL($data['post_identifier']);



            if(isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                try {

                  $path = Mage::getBaseDir('media').DS."em_blog".DS."posts".DS;

                  //Remove old picture
                  if($oldId = $this->getRequest()->getParam('id'))
                  {
                      $oldPictureLink = Mage::getModel('blog/post')->load($oldId)->getImage();
                      if(is_file($path.$oldPictureLink))
                          unlink($path.$oldPictureLink);

						//////////// Remove old picture thumbnail \\\\\\\\\\\\\\\\
						$thumnailWidth = Mage::getStoreConfig('blog/info/thumbnail_width');
						$thumnailHeight = Mage::getStoreConfig('blog/info/thumbnail_height');
						//// Thumbnail at category page ///////
						$resizePathThumbnail = $thumnailWidth."x".$thumnailHeight;
						$thumbnailLink = $path."thumbnail".DS.$resizePathThumbnail.DS.$oldPictureLink;
						if(is_file($thumbnailLink))
							unlink($thumbnailLink);
						//// Thumbnail at recent post block ////
						$resizePathThumbnail = Mage::getStoreConfig('blog/info/recent_thumbnail_width')."x".Mage::getStoreConfig('blog/info/recent_thumbnail_height');
						$thumbnailLink = $path."thumbnail".DS.$resizePathThumbnail.DS.$oldPictureLink;
						if(is_file($thumbnailLink))
							unlink($thumbnailLink);
						
						//Remove thumbnail at admin
						if(is_file($path."admin".DS."50x50".DS.$oldPictureLink))
							unlink($path."admin".DS."50x50".DS.$oldPictureLink);

                  }
                  

                  $uploader = new Varien_File_Uploader('image');
                  $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

                  $uploader->setAllowRenameFiles(false);

                  // setAllowRenameFiles(true) -> move your file in a folder the magento way
                  // setAllowRenameFiles(true) -> move your file directly in the $path folder
                  $uploader->setFilesDispersion(false);
                  
                  $imagetypes = array(
                            'image/png' => '.png',
                            'image/gif' => '.gif',
                            'image/jpeg' => '.jpg',
                            'image/bmp' => '.bmp');
                  $ext = $imagetypes[$_FILES['image']['type']];
                  $_FILES['image']['name'] = $data['post_identifier'].$ext;
                  
                  $uploader->save($path, $_FILES['image']['name']);

                  $data['image'] = $_FILES['image']['name'];


                  
                }catch(Exception $e) {

                }
              }
                else {

                  if(isset($data['image']['delete']) && $data['image']['delete'] == 1)
                      $data['image'] = '';
                  else
                      unset($data['image']);
              }


        $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
        
        try {
                if ($model->getData('post_on') == '') {
                        $model->setPostOn(now());
                }
                //$model->setPostIntro($data['post_intro']);
                $model->save();
                //print_r($model->getData());exit;
                $id = $model->getId();

                if($this->validate($data['post_identifier']))
                {
                    $data['post_identifier'] = $data['post_identifier'].'-'.$id;
                    $model->setPostIdentifier($data['post_identifier']);$model->save();
                }
                //print_r($model->load($id)->getData());exit;
                if($id)
                {
					if(isset($data['stores']))
						$model->setPostStore($data['stores'],$id);
					else
						$model->setPostStore(array(0),$id);
                    $tags_id   = $data['tags'];
                    $tags_name = $data['tags_name'];
                    if(count($tags_id))
                        $model->setTag($tags_id,$id,1,$tags_name);

                    //save relation of category and post
                    if($data['category_ids'])
                    {
                        if(substr($data['category_ids'],0,1) == ",")
                            $data['category_ids'] = substr( $data['category_ids'], 1 );
                        $data['category_ids'] = array_unique(explode(",",$data['category_ids']));
                        $model->setCatsOfPost($data['category_ids'],$id);
                    }
                    
                }

                //them hoac chinh sua du lieu cho blog_url_rewrite
                $dataBlogUrl = array('post_id' => $id,'tag_id'=>'','cat_id'=>'','request_path'=>$data['post_identifier'].'.html');
                Mage::helper('blog')->saveAndUpdateUrl($dataBlogUrl,$id,'post');
                
              
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blog')->__('Post was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));

                        return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find item to save'));
    $this->_redirect('*/*/');
    }

    public function deleteAction() {
            if( $this->getRequest()->getParam('id') > 0 ) {
                    try {
                            $model = Mage::getModel('blog/post')->load($this->getRequest()->getParam('id'));
                            $path = Mage::getBaseDir('media').DS."em_blog".DS."posts".DS;
                            $oldPictureLink = $model->getImage();
                            if(is_file($path.$oldPictureLink))
                                unlink($path.$oldPictureLink);

                              //Remove old picture thumbnail
                              $thumnailWidth = Mage::getStoreConfig('blog/info/thumbnail_width');
                              $thumnailHeight = Mage::getStoreConfig('blog/info/thumbnail_height');
                              $resizePathThumbnail = $thumnailWidth."x".$thumnailHeight;
                              $thumbnailLink = $path."thumbnail".DS.$resizePathThumbnail.DS.$oldPictureLink;
                              if(is_file($thumbnailLink))
                                  unlink($thumbnailLink);

                              //Remove thumbnail at admin
                              if(is_file($path."admin".DS."50x50".DS.$oldPictureLink))
                                  unlink($path."admin".DS."50x50".DS.$oldPictureLink);
                              $model->delete();

                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                            $this->_redirect('*/*/');
                    } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    }
            }
            $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $postIds = $this->getRequest()->getParam('post');
        if(!is_array($postIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $path = Mage::getBaseDir('media').DS."em_blog".DS."posts".DS;
                foreach ($postIds as $postId) {
                    $post = Mage::getModel('blog/post')->load($postId);
                    
                    $oldPictureLink = $post->getImage();
                    if(is_file($path.$oldPictureLink))
                        unlink($path.$oldPictureLink);

                      //Remove old picture thumbnail
                      $thumnailWidth = Mage::getStoreConfig('blog/info/thumbnail_width');
                      $thumnailHeight = Mage::getStoreConfig('blog/info/thumbnail_height');
                      $resizePathThumbnail = $thumnailWidth."x".$thumnailHeight;
                      $thumbnailLink = $path."thumbnail".DS.$resizePathThumbnail.DS.$oldPictureLink;
                      if(is_file($thumbnailLink))
                          unlink($thumbnailLink);

                      //Remove thumbnail at admin
                      if(is_file($path."admin".DS."50x50".DS.$oldPictureLink))
                          unlink($path."admin".DS."50x50".DS.$oldPictureLink);
                    $post->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($postIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $postIds = $this->getRequest()->getParam('post');
        if(!is_array($postIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($postIds as $postId) {
                    $blog = Mage::getSingleton('blog/post')
                        ->load($postId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($postIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'post.csv';
        $content    = $this->getLayout()->createBlock('blog/adminhtml_post_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'post.xml';
        $content    = $this->getLayout()->createBlock('blog/adminhtml_post_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function validate($identifier)
    {
        $blogUrl = Mage::getModel('blog/blogurl');
        return $blogUrl->validate($identifier,$this->getRequest()->getParam('id'),'post');
        
    }
	
}