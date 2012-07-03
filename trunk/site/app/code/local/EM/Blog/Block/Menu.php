<?php
class EM_Blog_Block_Menu extends Mage_Core_Block_Template
{
    public function drawMenuBlog($parentCat,$url = "")
    {
        $childs = Mage::getModel('blog/category')->getCollection();
		$childs->distinct(true);
		$childs->getSelect()
            ->join(
                array('cat_store'=>Mage::getSingleton('core/resource')->getTableName('blog_cat_store')),
                'cat_store.cat_id=main_table.id AND main_table.is_active=1 AND main_table.parent_id='.$parentCat->getId().'
				 AND cat_store.store_id in (0,'.Mage::app()->getStore()->getId().')',
                array()
             );

        $html = "";
        $count = 0;
		$curCatId = $this->getRequest()->getParam('cat');
		if($curCatId)
			$ids = Mage::getModel('blog/category')->load($curCatId)->getPathIds();
        foreach($childs as $c)
        {
            if($c->getChildrenCount() > 0)
                $parenIdClass = "parent";
            if($count == 0)
                $firstLast = "first";
            elseif($count == $childs->count()-1)
                $firstLast = "last";
            $level = $c->getLevel() - 2;
			if($curCatId){
				
				if(in_array($c->getId(),$ids))
				{
					$li = "<li class=' level$level $firstLast $parenIdClass current'>";
					$li .= "<a href='".$url.$c->getUrl().".html'><span>".$c->getCatName()."</span></a>";
				}
				else
				{
					$li = "<li class=' level$level $firstLast $parenIdClass'>";
					$li .= "<a href='".$url.$c->getUrl().".html'><span>".$c->getCatName()."</span></a>";
				}
			}
			else
			{
				$li = "<li class=' level$level $firstLast $parenIdClass'>";
				$li .= "<a href='".$url.$c->getUrl().".html'><span>".$c->getCatName()."</span></a>";
			}
            
            
            if($c->getChildrenCount() > 0)
            {
                $li .= "<ul class='level$level'>";
                $li .= $this->drawMenuBlog($c, $url.$c->getUrl().'/');
                $li .= "</ul>";
            }
            $li .= "</li>";
            
            $html .= $li;
            $count++;
        }
        
        return $html;
    }

    public function renderMenuBlog()
    {
        $root = Mage::getModel('blog/category')->load(2);
        return $this->drawMenuBlog($root,Mage::getUrl('blog'));
        
    }
}
