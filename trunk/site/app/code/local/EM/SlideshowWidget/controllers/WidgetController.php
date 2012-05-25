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
 * @package     Mage_Widget
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widgets management controller
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class EM_SlideshowWidget_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Wisywyg widget plugin main page
     */
    public function indexAction()
    {
        // save extra params for widgets insertion form
        $skipped = $this->getRequest()->getParam('skip_widgets');
        $skipped = Mage::getSingleton('widget/widget_config')->decodeWidgetsFromQuery($skipped);

        Mage::register('skip_widgets', $skipped);

        $this->loadLayout('empty')->renderLayout();
    }

    /**
     * Ajax responder for loading plugin options form
     */
    public function loadOptionsAction()
    {
        try {
            $this->loadLayout('empty');
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = Mage::helper('core')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->getLayout()->getBlock('wysiwyg_widget.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
						if(isset($request['values']['text1']))	$request['values']['text1']	= $this->html_decode($request['values']['text1']);
						if(isset($request['values']['text2']))	$request['values']['text2'] = $this->html_decode($request['values']['text2']);
						if(isset($request['values']['text3']))	$request['values']['text3'] = $this->html_decode($request['values']['text3']);
						if(isset($request['values']['text4']))	$request['values']['text4'] = $this->html_decode($request['values']['text4']);
						if(isset($request['values']['text5']))	$request['values']['text5'] = $this->html_decode($request['values']['text5']);
						if(isset($request['values']['text6']))	$request['values']['text6'] = $this->html_decode($request['values']['text6']);
						if(isset($request['values']['text7']))	$request['values']['text7'] = $this->html_decode($request['values']['text7']);
						if(isset($request['values']['text8']))	$request['values']['text8'] = $this->html_decode($request['values']['text8']);
						if(isset($request['values']['text9']))	$request['values']['text9'] = $this->html_decode($request['values']['text9']);
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                }
                $this->renderLayout();
            }
        } catch (Mage_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Format widget pseudo-code for inserting into wysiwyg editor
     */
    public function buildWidgetAction()
    {
        $type = $this->getRequest()->getPost('widget_type');
        $params = $this->getRequest()->getPost('parameters', array());
		if($type=='slideshowwidget/create')
		{	
			$params['text1']= $this->html_encode($params['text1']);
			$params['text2']= $this->html_encode($params['text2']);
			$params['text3']= $this->html_encode($params['text3']);
			$params['text4']= $this->html_encode($params['text4']);
			$params['text5']= $this->html_encode($params['text5']);
			$params['text6']= $this->html_encode($params['text6']);
			$params['text7']= $this->html_encode($params['text7']);
			$params['text8']= $this->html_encode($params['text8']);
			$params['text9']= $this->html_encode($params['text9']);
		}
        $asIs = $this->getRequest()->getPost('as_is');
        $html = Mage::getSingleton('widget/widget')->getWidgetDeclaration($type, $params, $asIs);
        $this->getResponse()->setBody($html);
    }
	
	protected function html_encode($str)
	{
		if($str	!=	''){
			$tam_1	=	str_replace('{','_!_ngvao_!_',$str);
			$tam_2	=	str_replace('}','_!_ngra_!_',$tam_1);
			$re		=	htmlspecialchars($tam_2);
			return $re;
		}
		else	return $str;
	}
	
	protected function html_decode($str)
	{
		if($str	!=	''){
			$tam_1	=	htmlspecialchars_decode($str);
			$tam_2	=	str_replace('_!_ngvao_!_','{',$tam_1);
			$re		=	str_replace('_!_ngra_!_','}',$tam_2);			
			return $re;
		}
		else	return $str;
	}
	
}
