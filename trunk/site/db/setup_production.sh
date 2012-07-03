mysqladmin -u em_demo --password=demo123 drop em0039
mysqladmin -u em_demo --password=demo123 create em0039
mysql -u em_demo --password=demo123 em0039 < 1_schema.sql
mysql -u em_demo --password=demo123 em0039 < 2_init_data.sql
mysql -u em_demo --password=demo123 em0039 < 3_PRODUCTION_update_config.sql
