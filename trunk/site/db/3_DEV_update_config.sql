update core_config_data set `value`='http://192.168.1.66/EM_Themes/em0039/site/' 
	where `path`='web/unsecure/base_url';
update core_config_data set `value`='http://192.168.1.66/EM_Themes/em0039/site/'' 
	where `path`='web/secure/base_url';
