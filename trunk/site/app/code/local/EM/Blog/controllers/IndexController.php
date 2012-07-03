<?php
class EM_Blog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $title = $this->getLayout()->getBlock('head')->getTitle();
        $this->getLayout()->getBlock('head')->setTitle("$title ".Mage::getStoreConfig('blog/info/page_title'));
        $this->renderLayout();
    }


    public function tagAction()
    {
        $id     = $this->getRequest()->getParam('tag_id');
        if($id)//load cac bai post co tag co khoa chinh la id hoac thuoc category co khoa chinh la id
        {
              $model  = Mage::getModel('blog/tag');
              $object = $model->load($id);

              if((int)$object->getStatus() == 1)//pending
              {
                      $this->_forward('noRoute');return;
              }
             
              $today = new DateTime(date('Y-m-d 00:00:00'));
              
              if(!$object->getCustomDesignFrom())
                   $fromDate = $today;
              else
                   $fromDate = new DateTime($object->getCustomDesignFrom());
              if(!$object->getCustomDesignTo())
                   $toDate = $today;
               else
                   $toDate = new DateTime($object->getCustomDesignTo());


              //print_r($object->getCustomDesignFrom());print_r($object->getCustomDesignTo());exit;
              if($today >= $fromDate && $today <= $toDate)
              {
                  $this->setTheme($object->getData('custom_design'), $object->getData('custom_layout_update_xml'), $object->getData('custom_layout'));
                  $title = $this->getLayout()->getBlock('head')->getTitle();
                  $this->getLayout()->getBlock('head')->setTitle("$title tag ".$object->getName());
                  $this->renderLayout();
              }
              else
              {
                  $this->loadLayout();
                  $title = $this->getLayout()->getBlock('head')->getTitle();
                  $this->getLayout()->getBlock('head')->setTitle("$title tag ".$object->getName());
                  $this->renderLayout();
              }
        }
        else
        {
            $this->loadLayout();
            $this->renderLayout();
        }
      



    }

    public function setTheme($design,$customLayouUpdateXML,$customLayout)
    {
          //$design = $tag->getData('custom_design');
          //========= set theme ===========
		  if(empty($design)){
				$design = Mage::getStoreConfig('design/package/name').'/'.Mage::getStoreConfig('design/theme/template');
		  }
          if($design)
          {
              list($package,$theme) = split('[/]', $design);
          }
          else
          {
              $package = 'default';
              $theme = 'default';
          }
          Mage::getSingleton('core/design_package')
              ->setPackageName($package)
              ->setTheme($theme);
          //====================
          $update = $this->getLayout()->getUpdate();
          $update->addHandle('default');
          $this->addActionLayoutHandles();
          $this->loadLayoutUpdates();
          $update->addUpdate($customLayouUpdateXML);
          $this->generateLayoutXml()->generateLayoutBlocks();
          //======= set template page (1column,2column ...) ============
          //$layoutCode = $tag->getData('custom_layout');
          if($customLayout)
          {
              $layout = Mage::getSingleton('page/config')->getPageLayout($customLayout);
              $template = $layout->getTemplate();
          
              $root = $this->getLayout()->getBlock('root');
              $root->setTemplate($template);
          }
    }

    public function catAction()
    {
        $id = $this->getRequest()->getParam('cat');
        
        if($id)//load cac bai post co tag co khoa chinh la id hoac thuoc category co khoa chinh la id
        {
              $model  = Mage::getModel('blog/category');
              $object = $model->load($id);
              //print_r($object->getStoreOfCat($id));exit;
              //echo Mage::app()->getStore()->getId();exit;
              if(!$object->getIsActive() || (!in_array(Mage::app()->getStore()->getId(),$object->getStoreOfCat($id)) && !in_array(0,$object->getStoreOfCat($id))) )
              {
                      $this->_forward('noRoute');return;
              }
           
              $today = new DateTime(date('Y-m-d 00:00:00'));
              if(!$object->getCustomDesignFrom())
                   $fromDate = $today;
              else
                   $fromDate = new DateTime($object->getCustomDesignFrom());
              if(!$object->getCustomDesignTo())
                   $toDate = $today;
               else
                   $toDate = new DateTime($object->getCustomDesignTo());
              
              if($today >= $fromDate && $today <= $toDate)
              {
                  $this->setTheme($object->getData('custom_design'), $object->getData('custom_layout_update_xml'), $object->getData('custom_layout'));
                  $title = $this->getLayout()->getBlock('head')->getTitle();
                  $this->getLayout()->getBlock('head')->setTitle("$title category ".$object->getPageTitle());
                  $this->getLayout()->getBlock('head')->setKeywords($object->getMetaKeywords());
                  $this->getLayout()->getBlock('head')->setDescription($object->getMetaDescription());
                  $this->renderLayout();
              }
              else
              {
                  $this->loadLayout();
                  $title = $this->getLayout()->getBlock('head')->getTitle();
                  $this->getLayout()->getBlock('head')->setTitle("$title category ".$object->getPageTitle());
                  $this->getLayout()->getBlock('head')->setKeywords($object->getMetaKeywords());
                  $this->getLayout()->getBlock('head')->setDescription($object->getMetaDescription());
                  $this->renderLayout();
              }
        }
        else
        {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function taglistAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    //public function prep


    public function viewAction()
    {
    	$id     = $this->getRequest()->getParam('id');
        $post = Mage::getModel('blog/post')->load($id);

        if(!$post->getStatus() || (!in_array(Mage::app()->getStore()->getId(),$post->getStoreOfPost($id)) && !in_array(0,$post->getStoreOfPost($id))) )
        {
                $this->_forward('noRoute');return;
        }

		$today = new DateTime(date('Y-m-d 00:00:00'));
        $fromDate = new DateTime($post->getCustomDesignFrom());
        if(!$post->getCustomDesignFrom())
           $fromDate = $today;
        else
           $fromDate = new DateTime($post->getCustomDesignFrom());
        if(!$post->getCustomDesignTo())
           $toDate = $today;
        else
           $toDate = new DateTime($post->getCustomDesignTo());

        if($today >= $fromDate && $today <= $toDate)
        {
            //========= set theme ===========
            $design = $post->getData('custom_design');
			if(empty($design)){
				$design = Mage::getStoreConfig('design/package/name').'/'.Mage::getStoreConfig('design/theme/template');
			}
            if($design)
            {
                list($package,$theme) = split('[/]', $design);
            }
            else
            {
                $package = 'default';
                $theme = 'default';
            }
            Mage::getSingleton('core/design_package')
                ->setPackageName($package)
                ->setTheme($theme);
            //====================
                $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');
            $this->addActionLayoutHandles();
            $this->loadLayoutUpdates();
            $update->addUpdate($post->getData('custom_layout_update_xml'));
            $this->generateLayoutXml()->generateLayoutBlocks();
            //======= set template page (1column,2column ...) ============
            $layoutCode = $post->getData('custom_layout');
            if($layoutCode)
            {
                $layout = Mage::getSingleton('page/config')->getPageLayout($layoutCode);
                $template = $layout->getTemplate();
                $root = $this->getLayout()->getBlock('root');
                $root->setTemplate($template);
            }
            
            
            //====================================
            $title = $this->getLayout()->getBlock('head')->getTitle();
            $this->getLayout()->getBlock('head')->setTitle($title." ".$post->getData('title'));
            
			
           
        }
        else
        {
            $this->loadLayout();
            $title = $this->getLayout()->getBlock('head')->getTitle();
            $this->getLayout()->getBlock('head')->setTitle($title." ".$post->getData('title'));

            
            
        }

        $keywords = $post->getData('post_meta_keywords');
        if(!$keywords)
        {
            $catId = $this->getRequest()->getParam('cat_id');
            if($catId)
            {
                $category = Mage::getModel('blog/category')->load($catId);
                $keywords = $category->getMetaKeywords();
            }

        }
        if($keywords)
            $this->getLayout()->getBlock('head')->setKeywords($keywords);
        $description = $post->getData('post_meta_description');
        if(!$description)
        {
            if(!$category)
            {
                $catId = $this->getRequest()->getParam('cat_id');
                if($catId)
                {
                    $category = Mage::getModel('blog/category')->load($catId);
                    $description = $category->getMetaDescription();
                }
            }
            else
            {
                $description = $category->getMetaDescription();
            }
        }
        if($description)
            $this->getLayout()->getBlock('head')->setDescription($description);
        $this->renderLayout();
    }
    public function view2Action()
    {
        $id     = $this->getRequest()->getParam('id');
        $modelPost  = Mage::getModel('blog/post');
        $model = $modelPost->load($id);
        $layoutCode = $model->getData('custom_layout');
        $layout = Mage::getSingleton('page/config')->getPageLayout($layoutCode);
        $template = $layout->getTemplate();
        
        //====================
        $design = $model->getData('custom_design');
        if($design)
        {
        	list($package,$theme) = split('[/]', $design);
        }
        else
        {
        	$package = 'default';
        	$theme = 'default';
        }
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
        //====================

		$update = $this->getLayout()->getUpdate();
		//$this->addActionLayoutHandles();
		
		$update->addUpdate('
			<reference name="right"><block type="reports/product_compared" name="home.reports.product.compared" template="reports/home_product_compared.phtml" after="product_viewed"><action method="addPriceBlockType"><type>bundle</type><block>bundle/catalog_product_price</block><template>bundle/catalog/product/price.phtml</template></action></block></reference>
		');
		//$this->generateLayoutXml()->generateLayoutBlocks();
		$this->loadLayoutUpdates();
		
        $this->loadLayout();
        
        //$root = $this->getLayout()->getBlock('root');
		//$root->setTemplate($template);

		$this->renderLayout();
    }
    
    public function checkRecaptcha()
    {
        include_once("recaptchalib.php");
        $privatekey = Mage::getStoreConfig('blog/recaptcha/private_key');
        if ($this->getRequest()->getPost("recaptcha_response_field")) {
            $resp = recaptcha_check_answer ($privatekey,
                                                $_SERVER["REMOTE_ADDR"],
                                                $_POST["recaptcha_challenge_field"],
                                                $_POST["recaptcha_response_field"]);
            //header('Content-Type: text/html; charset=utf-8');
            return $resp->is_valid;
        }
    }
    
    public function newcommentAction()
    {
        if(Mage::getStoreConfig('blog/recaptcha/enable_recapcha'))
            $recaptcha = $this->checkRecaptcha();
        else
            $recaptcha = 1;
        if($recaptcha == 1)
        {
              $data = $this->getRequest()->getPost();
              unset($data['submit']);
              

              $data['time'] = date('o-m-d H:i:s');
              if(Mage::helper('customer')->isLoggedIn())//user login
              {
                  $customer = Mage::getSingleton('customer/session')->getCustomer();
                  $data['username'] = $customer->getName();
                  $data['email'] = $customer->getEmail();
              }

              if(Mage::getStoreConfig('blog/comments/auto_approved'))
                  $data['status_comment'] = 2;
              elseif(Mage::getStoreConfig('blog/comments/auto_approved_login') && Mage::helper('customer')->isLoggedIn())
                  $data['status_comment'] = 2;

              $data['comment_content'] = nl2br(htmlspecialchars($data['comment_content']));

              $model = Mage::getModel('blog/comment')->setData($data);





              try {
                            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                            $query = "SET FOREIGN_KEY_CHECKS=0;";
                            $write->query($query);

                              $insertId = $model->save()->getId();
                              //echo $insertId;
                              if($data['parent_id'] == 0)
                              {
                                      $data['parent_id'] = $insertId;
                                      $model = Mage::getModel('blog/comment')->load($insertId)->addData($data);
                                      try {
                                                      $model->setId($insertId)->save();
                                                      //echo "Data updated successfully.";

                                              } catch (Exception $e){
                                                      //echo $e->getMessage();
                                      }
                              }
                      } catch (Exception $e){
                       echo $e->getMessage();
              }


              /////Send Mail for this Comment/////////

              if (Mage::getStoreConfig('blog/comments/recipient_email') != null && isset($insertId)) {
                  $translate = Mage::getSingleton('core/translate');
                    /* @var $translate Mage_Core_Model_Translate */
                    $translate->setTranslateInline(false);
                    try {
                            $data["url"] = Mage::getBaseUrl().trim($data['uri'],'/');
                            $postObject = new Varien_Object();
                            $postObject->setData($data);
                            $mailTemplate = Mage::getModel('core/email_template');
                            /* @var $mailTemplate Mage_Core_Model_Email_Template */
                            $mailTemplate->sendTransactional(
                                            Mage::getStoreConfig('blog/comments/email_template'),
                                            Mage::getStoreConfig('blog/comments/sender_email_identity'),
                                            Mage::getStoreConfig('blog/comments/recipient_email'),
                                            null,
                                            array('data' => $postObject)
                            );
                            $translate->setTranslateInline(true);
                    } catch (Exception $e) {
                            $translate->setTranslateInline(true);
                    }
                }
                /////Send Mail for this Comment/////////


                  echo "1";
        }
        else
                echo "0";
		
        
        exit;
        
        //$this->_redirect('*/*/view', array('id' => $data['post_id']));
        //print_r($data);exit;
    }
	
	
}
