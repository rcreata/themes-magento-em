<?php

class EM_Blog_Block_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
      $image = $row->getData('image');
      $path = substr(Mage::getBaseUrl(),0,strpos(Mage::getBaseUrl(),'index.php')).'media/em_blog/posts/'.$image;
      //$path = Mage::getBaseDir().DS."skin".DS."frontend".DS."base".DS."default".DS."em_blog".DS."images".DS."$image;
    	if (empty($path)) return '';
    	return '<img src="'.$path.'" style="width:55px;height:45px;"/>';
    }

}