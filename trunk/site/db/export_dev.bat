C:\xampp\mysql\bin\mysqldump -d --comments=FALSE -u root testtb > 1_schema.sql 
C:\xampp\mysql\bin\mysqldump  -t --order-by-primary --comments=FALSE -u root testtb > 2_init_data.sql 
