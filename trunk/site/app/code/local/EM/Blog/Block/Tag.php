<?php
class EM_Blog_Block_Tag extends Mage_Core_Block_Template
{
    public function getTagByPost()
    {
        $postId = $this->getRequest()->getParam('id');
        $tags = Mage::getModel('blog/tag')->getCollection();
        $tags->getSelect()
                ->join(
                        array('tag_post'=>Mage::getSingleton('core/resource')->getTableName('blog_tag_post')),
                        "tag_post.tag_id=main_table.id 
                        AND tag_post.post_id=$postId
                        AND main_table.status=0",
                        array('post_id'=>'tag_post.post_id')
                );
        return $tags;
    }
}
