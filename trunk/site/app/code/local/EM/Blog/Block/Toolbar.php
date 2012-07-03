<?php
class EM_Blog_Block_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected function _construct()
    {
        parent::_construct();
        //$this->_orderField  = 'title';
        //load avaiable limit
    	/*$arr= explode(',','2,3');
		$availableLimit = array();
		foreach($arr as $value){
			$value = trim($value,' ');
			$this->_availableLimit['detail'][$value] =$value;
			$this->_availableLimit['simple'][$value] = $value;
		}*/
        $this->_availableOrder=array('main_table.title'=>$this->__('Name'),'main_table.time'=>$this->__('Time'));
        $this->_availableMode = array('detail' =>  $this->__('Detail'),'simple' => $this->__('Simple'));
        $this->setPageVarName('page');
        $this->getCollection($this->getCollection());
        $this->setTemplate('em_blog/toolbar.phtml');
    }
    public function getAvailableLimit()
    {
        $currentMode = $this->getCurrentMode();
        if (in_array($currentMode, array('detail', 'simple'))) {
            return $this->_availableLimit[$currentMode];
        } else {
            return $this->_defaultAvailableLimit;
        }
    }

    public function getPagerHtml()
    {
        $pagerBlock = $this->getChild('em_blog_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setShowPerPage(false);
            $pagerBlock->setUseContainer(false)
                ->setShowAmounts(false);
            $pagerBlock->setLimitVarName($this->getLimitVarName())
                ->setPageVarName('page')
                ->setLimit($this->getLimit())
                //->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                //->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

            return $pagerBlock->toHtml();
        }

        return '';
    }

}
