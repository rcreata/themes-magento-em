<?php

$installer = $this;

$installer->startSetup();

$installer->run("
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS {$this->getTable('blog_url_rewrite')};
DROP TABLE IF EXISTS {$this->getTable('blog_tag_post')};
DROP TABLE IF EXISTS {$this->getTable('blog_post_store')};
DROP TABLE IF EXISTS {$this->getTable('blog_cat_store')};
DROP TABLE IF EXISTS {$this->getTable('blog_comment')};
DROP TABLE IF EXISTS {$this->getTable('blog_category_post')};
DROP TABLE IF EXISTS {$this->getTable('blog_tag')};
DROP TABLE IF EXISTS {$this->getTable('blog_category')};
DROP TABLE IF EXISTS {$this->getTable('blog_post')};

CREATE TABLE IF NOT EXISTS {$this->getTable('blog_category')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) DEFAULT NULL,
  `is_active` int(1) NOT NULL,
  `description` text,
  `image` varchar(555) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `meta_keywords` text,
  `meta_description` text,
  `url` varchar(255) NOT NULL,
  `display_mode` int(11) NOT NULL,
  `cms_block` int(11) DEFAULT NULL,
  `custom_design` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_design_from` datetime DEFAULT NULL,
  `custom_design_to` datetime DEFAULT NULL,
  `custom_layout` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_layout_update_xml` varchar(8000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `level` int(11) DEFAULT '0',
  `path` varchar(255) DEFAULT '1',
  `children_count` int(11) DEFAULT '0',
  `show_image` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cms_block` (`cms_block`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6466 ;

ALTER TABLE {$this->getTable('blog_category')}
  ADD CONSTRAINT `{$this->getTable('blog_category')}_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES {$this->getTable('blog_category')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS {$this->getTable('blog_post')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `post_on` datetime NOT NULL,
  `post_by` mediumint(9) unsigned NOT NULL,
  `post_identifier` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) NOT NULL COMMENT '1:enabled,0:disabled',
  `allow_comment` smallint(1) NOT NULL COMMENT '0:only login user,1:every one,2:stop',
  `post_content` text COLLATE utf8_unicode_ci NOT NULL,
  `post_content_heading` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `custom_design` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_design_from` datetime DEFAULT NULL,
  `custom_design_to` datetime DEFAULT NULL,
  `custom_layout` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_layout_update_xml` varchar(8000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_meta_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `post_meta_description` text COLLATE utf8_unicode_ci NOT NULL,
  `post_intro` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_by` (`post_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

ALTER TABLE {$this->getTable('blog_post')}
  ADD CONSTRAINT `{$this->getTable('blog_post')}_ibfk_1` FOREIGN KEY (`post_by`) REFERENCES {$this->getTable('admin_user')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;





CREATE TABLE IF NOT EXISTS {$this->getTable('blog_tag')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) NOT NULL COMMENT '0:approved,1:pending',
  `custom_design` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_design_from` datetime DEFAULT NULL,
  `custom_design_to` datetime DEFAULT NULL,
  `custom_layout` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `custom_layout_update_xml` varchar(8000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag_identifier` varchar(555) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;





CREATE TABLE IF NOT EXISTS {$this->getTable('blog_comment')} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_content` text COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `time` datetime NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `status_comment` int(11) DEFAULT '1' COMMENT '0: disable , 1 : pending , 2 : approved',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`parent_id`),
  KEY `parent_id` (`parent_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

ALTER TABLE {$this->getTable('blog_comment')}
  ADD CONSTRAINT `{$this->getTable('blog_comment')}_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES {$this->getTable('blog_comment')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_comment')}_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES {$this->getTable('blog_post')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;





CREATE TABLE IF NOT EXISTS {$this->getTable('blog_category_post')} (
  `cat_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE {$this->getTable('blog_category_post')}
  ADD CONSTRAINT `{$this->getTable('blog_category_post')}_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES {$this->getTable('blog_post')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_category_post')}_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES {$this->getTable('blog_category')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;





CREATE TABLE IF NOT EXISTS {$this->getTable('blog_cat_store')} (
  `cat_id` int(11) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`cat_id`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE {$this->getTable('blog_cat_store')}
  ADD CONSTRAINT `{$this->getTable('blog_cat_store')}_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_cat_store')}_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES {$this->getTable('blog_category')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS {$this->getTable('blog_post_store')} (
  `post_id` int(11) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE {$this->getTable('blog_post_store')}
  ADD CONSTRAINT `{$this->getTable('blog_post_store')}_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_post_store')}_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES {$this->getTable('blog_post')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS {$this->getTable('blog_tag_post')} (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE {$this->getTable('blog_tag_post')}
  ADD CONSTRAINT `{$this->getTable('blog_tag_post')}_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES {$this->getTable('blog_tag')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_tag_post')}_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES {$this->getTable('blog_post')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE IF NOT EXISTS {$this->getTable('blog_url_rewrite')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `cat_id` int(11) NOT NULL,
  `request_path` varchar(555) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;



ALTER TABLE {$this->getTable('blog_url_rewrite')}
  ADD CONSTRAINT `{$this->getTable('blog_url_rewrite')}_ibfk_3` FOREIGN KEY (`tag_id`) REFERENCES {$this->getTable('blog_tag')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_url_rewrite')}_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES {$this->getTable('blog_post')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `{$this->getTable('blog_url_rewrite')}_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES {$this->getTable('blog_category')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO {$this->getTable('blog_category')} (`id`, `cat_name`, `is_active`, `description`, `image`, `page_title`, `meta_keywords`, `meta_description`, `url`, `display_mode`, `cms_block`, `custom_design`, `custom_design_from`, `custom_design_to`, `custom_layout`, `custom_layout_update_xml`, `parent_id`, `level`, `path`, `children_count`) VALUES
(1, 'root', 1, NULL, NULL, NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '1', 8),
(2, 'root category', 1, NULL, NULL, NULL, NULL, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '1/2', 7);
	");
	

	

$installer->endSetup(); 