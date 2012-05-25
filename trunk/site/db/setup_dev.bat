E:\xampp\mysql\bin\mysqladmin -u root drop em0039
E:\xampp\mysql\bin\mysqladmin -u root create em0039
E:\xampp\mysql\bin\mysql -u root em0039 < 1_schema.sql
E:\xampp\mysql\bin\mysql -u root em0039 < 2_init_data.sql
E:\xampp\mysql\bin\mysql -u root em0039 < 3_DEV_update_config.sql