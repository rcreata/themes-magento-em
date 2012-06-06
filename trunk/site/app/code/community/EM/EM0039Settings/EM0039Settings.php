<?php
class EM_EM0039Settings_EM0039Settings 
{
	public function get_grid_thumb_width()
	{
		return Mage::getStoreConfig('em0039/image/grid_thumb_width');
	}
	public function get_grid_thumb_height()
	{
		return Mage::getStoreConfig('em0039/image/grid_thumb_height');
	}
	public function get_grid_thumb_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/grid_thumb_bgcolor');
	}
	public function get_listing_thumb_width()
	{
		return Mage::getStoreConfig('em0039/image/listing_thumb_width');
	}
	public function get_listing_thumb_height()
	{
		return Mage::getStoreConfig('em0039/image/listing_thumb_height');
	}
	public function get_listing_thumb_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/listing_thumb_bgcolor');
	}
	public function get_base_image_width()
	{
		return Mage::getStoreConfig('em0039/image/base_image_width');
	}
	public function get_base_image_height()
	{
		return Mage::getStoreConfig('em0039/image/base_image_height');
	}
	public function get_base_image_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/base_image_bgcolor');
	}
	public function get_thumb_base_width()
	{
		return Mage::getStoreConfig('em0039/image/thumb_base_width');
	}
	public function get_thumb_base_height()
	{
		return Mage::getStoreConfig('em0039/image/thumb_base_height');
	}
	public function get_thumb_base_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/thumb_base_bgcolor');
	}
	public function get_related_width()
	{
		return Mage::getStoreConfig('em0039/image/related_width');
	}
	public function get_related_height()
	{
		return Mage::getStoreConfig('em0039/image/related_height');
	}
	public function get_related_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/related_bgcolor');
	}
	public function get_crosssell_width()
	{
		return Mage::getStoreConfig('em0039/image/crosssell_width');
	}
	public function get_crosssell_height()
	{
		return Mage::getStoreConfig('em0039/image/crosssell_height');
	}
	public function get_crosssell_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/crosssell_bgcolor');
	}
	public function get_upsell_width()
	{
		return Mage::getStoreConfig('em0039/image/upsell_width');
	}
	public function get_upsell_height()
	{
		return Mage::getStoreConfig('em0039/image/upsell_height');
	}
	public function get_upsell_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/upsell_bgcolor');
	}
	public function get_widget_width()
	{
		return Mage::getStoreConfig('em0039/image/widget_width');
	}
	public function get_widget_height()
	{
		return Mage::getStoreConfig('em0039/image/widget_height');
	}
	public function get_widget_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/widget_bgcolor');
	}
	public function get_mostpopular_width()
	{
		return Mage::getStoreConfig('em0039/image/mostpopular_width');
	}
	public function get_mostpopular_height()
	{
		return Mage::getStoreConfig('em0039/image/mostpopular_height');
	}
	public function get_mostpopular_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/mostpopular_bgcolor');
	}
	public function get_tab_width()
	{
		return Mage::getStoreConfig('em0039/image/tab_width');
	}
	public function get_tab_height()
	{
		return Mage::getStoreConfig('em0039/image/tab_height');
	}
	public function get_tab_bgcolor()
	{
		return Mage::getStoreConfig('em0039/image/tab_bgcolor');
	}
	
	public function get_column_count(){
		$nameTheme = 'em0039';
		$curTemplate = $this->getCurrentTemplate();
		$availableColumnCount = array(
			'empty'				=>	5,
			'1column'		=>	Mage::getStoreConfig($nameTheme.'/image/cat_one_column'),
			'2columns-left'	=>	Mage::getStoreConfig($nameTheme.'/image/cat_two_columns'),
			'2columns-right'	=>	Mage::getStoreConfig($nameTheme.'/image/cat_two_columns'),
			'3columns'		=>	Mage::getStoreConfig($nameTheme.'/image/cat_three_columns')
		);	
		return $availableColumnCount[$curTemplate];
	}
	
	public function getCurrentTemplate(){
		return str_replace(array('page/','.phtml'),'',Mage::app()->getLayout()->getBlock('root')->getTemplate());
	}
}