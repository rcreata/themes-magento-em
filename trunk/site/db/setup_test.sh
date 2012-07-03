mysqladmin -u root drop em0039
mysqladmin -u root create em0039
mysql -u root em0039 < 1_schema.sql
mysql -u root em0039 < 2_init_data.sql
mysql -u root em0039 < 3_TEST_update_config.sql