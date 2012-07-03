<?php
class EM_Blog_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{
    
    public function _initCurrentCategory()
    {
        $id     = $this->getRequest()->getParam('id');
        $category  = Mage::getModel('blog/category');
        if($id)
        {
            $category  = Mage::getModel('blog/category')->load($id);
            $category->setData('store_id',$category->getStoreOfCat($id));
        }
        Mage::register('cat_data', $category);
    }

    protected function _initAction() {
            $this->loadLayout()
                    ->_setActiveMenu('blog/items')
                    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

            return $this;
    }

    public function indexAction() {

            $this->_initAction()
                    ->renderLayout();
    }

    public function editAction() {

            

                    /*$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                    //print_r($data);exit;
                    if (!empty($data)) {
                      //echo 'abc';exit;
                            $model->setData($data);
                    }
                    $model->setData('store_id',$model->getStoreOfCat($id));
                    Mage::register('cat_data', $model);
*/
        $this->_initCurrentCategory();
                    $this->loadLayout();
                    $this->_setActiveMenu('blog/items');

                    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                    $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_category_edit'))
                            ->_addLeft($this->getLayout()->createBlock('blog/adminhtml_category_edit_tabs'));

                    $this->renderLayout();
                    //echo 'abc';exit;
           
    }

    public function newAction() {
        $this->_initCurrentCategory();
                    $this->loadLayout();
                    $this->_setActiveMenu('blog/items');

                    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                    $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

                    $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                    $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_category_edit'))
                            ->_addLeft($this->getLayout()->createBlock('blog/adminhtml_category_edit_tabs'));

                    $this->renderLayout();
            //$this->_forward('edit');
    }

	
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/category');
            if(!$data['url'])
                $data['url'] = Mage::helper('blog/post')->friendlyURL($data['cat_name']);
            else
                $data['url'] = Mage::helper('blog/post')->friendlyURL($data['url']);
            unset($data['id']);
            if(!$data['custom_design_from'])
               $data['custom_design_from'] = NULL;
            if(!$data['custom_design_to'])
               $data['custom_design_to'] = NULL;
            //$data['path'] = '1';$data['children_count'] = 0;
            //$model->setData($data)->save();
            
            try {
                //Upload image
                if(isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                    $pathImage = Mage::getBaseDir('media').DS."em_blog".DS."category".DS;
                    //Remove old picture
                  if($oldId = $this->getRequest()->getParam('id'))
                  {
                      $oldPictureLink = Mage::getModel('blog/category')->load($oldId)->getImage();
                      if(is_file($pathImage.$oldPictureLink))
                          unlink($pathImage.$oldPictureLink);
                      //Remove thumbnail at admin
                      if(is_file($pathImage."admin".DS."50x50".DS.$oldPictureLink))
                          unlink($pathImage."admin".DS."50x50".DS.$oldPictureLink);
                  }


            try {
                  $uploader = new Varien_File_Uploader('image');
                  $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

                  $uploader->setAllowRenameFiles(true);
                  $imagetypes = array(
                        'image/png' => '.png',
                        'image/gif' => '.gif',
                        'image/jpeg' => '.jpg',
                        'image/bmp' => '.bmp');
                  $ext = $imagetypes[$_FILES['image']['type']];

                  $_FILES['image']['name'] = $data['url'].$ext;

                  // setAllowRenameFiles(true) -> move your file in a folder the magento way
                  // setAllowRenameFiles(true) -> move your file directly in the $path folder
                  $uploader->setFilesDispersion(false);
                    //D:\xampp1.7.1\htdocs\magento_3
                  //$path = substr(Mage::getBaseUrl(),0,strpos(Mage::getBaseUrl(),'index.php')).'skin/frontend/base/default/images/blog/';
                  
                  $uploader->save($pathImage, $_FILES['image']['name']);
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
                //End Upload Image
                
                //Set Children Count ,Level and Path for Parent Category
                if(!$curId = $this->getRequest()->getParam('id'))//add new category
                {
                    $parent = Mage::getModel('blog/category')->load($data['parent_id']);
                    $parent->setChildrenCount((int)$parent->getChildrenCount()+1);
                    $parent->save();
                    $data['level'] = (int)$parent->getLevel()+1;
                    //$model->setLevel((int)$parent->getLevel()+1);
                    $path = $parent->getPath();
                    $rootId = Mage::getStoreConfig('blog/info/root_id');
                    $parent = Mage::getModel('blog/category')->load($parent->getParentId());
                    while($parent->getId() != $rootId)
                    {
                        $parent->setChildrenCount((int)$parent->getChildrenCount()+1);
                        $parent->save();
                        $parent = Mage::getModel('blog/category')->load($parent->getParentId());
                    }
                    //update children count for root category
                    $parent = Mage::getModel('blog/category')->load($parent->getParentId());
                    $parent->setChildrenCount((int)$parent->getChildrenCount()+1);
                    $parent->save();
                }
                else//edit existed category
                {
                    //Parent Category is changed
                    $category = Mage::getModel('blog/category')->load($curId);
               
                    if($data['parent_id'] != $category->getParentId())
                    {
                        $newParentCat = Mage::getModel('blog/category')->load($data['parent_id']);

                        $data['level'] = (int)$newParentCat->getLevel() + 1;
                        //$model->setLevel((int)$newParentCat->getLevel() + 1);
                        $path = $newParentCat->getPath();
                        
                        //Update path for children category of current category
                        $count = 0;
                        $childCollection = Mage::getModel('blog/category')->getCollection()
                                    ->addFieldToFilter('path',array('like'=>"%/$curId/%"));
                        
                        foreach($childCollection as $c)
                        {
                            $c->setPath($path.strstr($c->getPath(),"/$curId/"));
                            $c->setLevel(count(explode("/",$c->getPath()))-1);
                            $c->save();
                            
                            $count++;
                        }
                        //Update children count for parent categories
                        $parent = Mage::getModel('blog/category')->load($category->getParentId());
                        if(!strstr($parent->getPath(),"/".$newParentCat->getId()."/"))
                        {
                            $newParentCat->setChildrenCount($newParentCat->getChildrenCount()+$count+1);
                            $newParentCat->save();
                            while($parent->getId() != Mage::getStoreConfig('blog/info/root_category_id') && !strstr($newParentCat->getPath(),$parent->getPath()))
                            {
                                $parent->setChildrenCount($parent->getChildrenCount()-$category->getChildrenCount()-1);
                                $parent->save();
                                $parent = Mage::getModel('blog/category')->load($parent->getParentId());
                            }
                        }
                        else
                        {
                            $parent->setChildrenCount($parent->getChildrenCount()-$category->getChildrenCount()-1);
                            $parent->save();
                        }
                        


                    }
                }

                //$model->setId($curId)->setData($data);
                
                      //          print_r($model->getData());exit;
                $model->setData($data);//print_r($model->getData());exit;
                if($curId)
                {
                    $id = $curId;
                    $model->setId($curId);
                    if($path)
                        $model->setPath($path.'/'.$curId);
                    $model->save();
                }else
                {
                    $model->save();
                    $id = $model->getId();
                    $model->setPath($path.'/'.$model->getId())->save();
                }
                
                //print_r($model->getData());exit;
                $model->setPostsOfCat($data['products'],$model);
				if(isset($data['stores']))
					$model->setCatStore($data['stores'],$id);
				else	
					$model->setCatStore(array(0),$id);
                //them hoac chinh sua du lieu cho blog_url_rewrite
                $dataBlogUrl = array('post_id' => '','tag_id'=>'','cat_id'=>$id,'request_path'=>$data['url'].'.html');
                Mage::helper('blog')->saveAndUpdateUrl($dataBlogUrl,$id,'cat');
                ///////////ket thuc sua bog_url_rewrite////////////////


                 //Mage::helper('blog')->saveAndUpdateUrl($dataBlogUrl,$this->getRequest()->getParam('id'),'Category');
                 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blog')->__('Category was successfully saved'));

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

    public function updateChildrenCount($category)
    {
        if($category)
        {
            $rootId = Mage::getStoreConfig('blog/info/root_id');
            $count = (int)$category->getChildrenCount() + 1 ;
            $parentCat = Mage::getModel('blog/category')->load($category->getParentId());
            
            while ($parentCat->getId() != $rootId)
            {
                $parentCat->setChildrenCount($parentCat->getChildrenCount() - $count);
                $parentCat->save();
                $parentCat = Mage::getModel('blog/category')->load($parentCat->getParentId());
            }
            $parentCat->setChildrenCount($parentCat->getChildrenCount() - $count);
            $parentCat->save();
        }
    }


    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('blog/category')->load($this->getRequest()->getParam('id'));
                $this->updateChildrenCount($model);
                $resource = Mage::getSingleton('core/resource');
                $write = $resource->getConnection('core_write');

                ///////////Remove piture of this category///////////
                $pathImage = Mage::getBaseDir('media').DS."em_blog".DS."category".DS;
                $oldPictureLink = $model->getImage();
                if(is_file($pathImage.$oldPictureLink))
                    unlink($pathImage.$oldPictureLink);
                //Remove thumbnail at admin
                if(is_file($pathImage."admin".DS."50x50".DS.$oldPictureLink))
                    unlink($pathImage."admin".DS."50x50".DS.$oldPictureLink);
                ///////////Remove piture of this category///////////

                $write->query('delete from '.$resource->getTableName('blog_category').' where id='.$model->getId());
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
        $catIds = $this->getRequest()->getParam('cat');
        if(!is_array($catIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            
                $delete = array();
                foreach ($catIds as $catId) {
                    $category = Mage::getModel('blog/category')->load($catId);
                    if(!in_array($category->getParentId(), $catIds))
                    {
                        $delete[] = $category;
                    }
                }
                $resource = Mage::getSingleton('core/resource');
                $write = $resource->getConnection('core_write');
                $pathImage = Mage::getBaseDir('media').DS."em_blog".DS."category".DS;
                foreach($delete as $d)
                {
                    $this->updateChildrenCount($d);

                    ///////////Remove piture of this category///////////
                    
                    $oldPictureLink = $d->getImage();
                    if(is_file($pathImage.$oldPictureLink))
                        unlink($pathImage.$oldPictureLink);
                    //Remove thumbnail at admin
                    if(is_file($pathImage."admin".DS."50x50".DS.$oldPictureLink))
                        unlink($pathImage."admin".DS."50x50".DS.$oldPictureLink);
                    ///////////Remove piture of this category///////////

                    $write->query('delete from '.$resource->getTableName('blog_category').' where id='.$d->getId());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($catIds)
                    )
                );
            
        }
        $this->_redirect('*/*/index');
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
	
    public function massStatusAction()
    {
        $catIds = $this->getRequest()->getParam('cat');
        if(!is_array($catIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($catIds as $catId) {
                    $blog = Mage::getSingleton('blog/category')
                        ->load($catId)
                        ->setIsActive($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($catIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'cat.csv';
        $content    = $this->getLayout()->createBlock('blog/adminhtml_category_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'cat.xml';
        $content    = $this->getLayout()->createBlock('blog/adminhtml_category_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _initCategory($getRootInstead = false)
    {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Categories'))
             ->_title($this->__('Manage Categories'));

        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('blog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            /*if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    }
                    else {
                        
                        return false;
                    }
                }
            }*/
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('cat_data', $category);
        Mage::register('current_cat_data', $category);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $category;
    }

    public function gridAction()
    {
        if (!$category = $this->_initCategory(true)) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('blog/adminhtml_category_edit_tab_articles')->toHtml()
        );
    }

    public function validateAction()
    {
        $url = $this->getRequest()->getPost('url');
        if(!$url)
            $url = Mage::helper('blog/post')->friendlyURL($this->getRequest()->getPost('cat_name'));
        else
            $url = Mage::helper('blog/post')->friendlyURL($url);
        $response = new Varien_Object();
        $response->setError(false);
        $blogUrl = Mage::getModel('blog/blogurl');
        $flag = $blogUrl->validate($url,$this->getRequest()->getParam('id'),'cat');
        if($flag)
        {
            $response->setError(true);
            $response->setAttribute("Url");
            $response->setMessage("The value of url is unique");
            $response->setData('identifier',$url );
        }
        
        $this->getResponse()->setBody($response->toJson());
    }

}
