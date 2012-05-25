<?php
class EM_Ajaxcart_IndexController extends Mage_Core_Controller_Front_Action
{   
	public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
			
    public function addAction()
    {				
		$id = $this->getRequest()->getParam('id');
		if ($id) {
			try {
				Mage::getSingleton('checkout/cart')->removeItem($id)
				  ->save();
			} catch (Exception $e) {
				Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
			}
		}
            

        if ($this->getRequest()->getParam('product')) {
            $cart   = Mage::getSingleton('checkout/cart');
            $params = $this->getRequest()->getParams();
            $related = $this->getRequest()->getParam('related_product');

            $productId = (int) $this->getRequest()->getParam('product');


            if ($productId) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                try {
                    if (!isset($params['qty'])) {
                        $params['qty'] = 1;
                    }

                    $cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $cart->addProductsByIds(explode(',', $related));
                    }
                    if($cart->save()){					
					   Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
					   Mage::getSingleton('checkout/session')->setCartInsertedItem($product->getId());

						$img = '';
						Mage::dispatchEvent('checkout_cart_add_product_complete', array('product'=>$product, 'request'=>$this->getRequest())); 
						$message = $this->__('%s was successfully added to your shopping cart.', $product->getName());					
						$img = "<img src='".Mage::helper('catalog/image')->init($product, 'image')->resize(60,null)."' />";
						$tmp_product = '<div class="ajaxcart_image">'.$img.'</div><div class="ajaxcart_message">'.$message.'</div>';
						
						Mage::getSingleton('checkout/session')->addSuccess($tmp_product);

						$check_addtocart = Mage::registry('check_addtocart');		
						if($check_addtocart)
							Mage::unregister('check_addtocart');
						Mage::register('check_addtocart',1);
					}
                }
                catch (Mage_Core_Exception $e) {	
						echo '<div class="ajc_error">add ajaxcart</div>';					   
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            Mage::getSingleton('checkout/session')->addError($message);
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
                }
				
            }
        }		
		$smessages = Mage::getSingleton('checkout/session')->getMessages()->getItems();
		$output = NULL;
		foreach ($smessages as $smessage) {
			  $output .= $smessage->getText();
		}		
		$msg = Mage::registry('msg');		
		if($msg)
			Mage::unregister('msg');
		Mage::register('msg',$output);
		
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
       
    }
	
	 public function deleteAction()
    {       		
	
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)
                  ->save();
            } catch (Exception $e) {
               Mage::getSingleton('checkout/cart')->addError($this->__('Cannot remove item'));
            }
        }
		$backUrl = $this->_getRefererUrl();
		$this->getResponse()->setRedirect($backUrl);
        // $this->loadLayout();
        // $this->_initLayoutMessages('checkout/session');
        // $this->renderLayout();
    }
	
	public function addtocartAction()
    {
        $this->indexAction();
    }
	
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }

    
}