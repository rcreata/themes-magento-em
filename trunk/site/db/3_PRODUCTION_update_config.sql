update core_config_data set `value`='http://www.emthemes.com/demo/magento-theme-cafeland-em0039/' 
	where `path`='web/unsecure/base_url';
update core_config_data set `value`='http://www.emthemes.com/demo/magento-theme-cafeland-em0039/' 
	where `path`='web/secure/base_url';

update core_config_data set `value`='0' 
		where `path`='web/seo/use_rewrites';
