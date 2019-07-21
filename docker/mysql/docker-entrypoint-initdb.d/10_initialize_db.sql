CREATE DATABASE IF NOT EXISTS a6s_cloud;
-- ALTER USER 'default'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';
CREATE USER 'default'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';
GRANT ALL ON a6s_cloud.* TO 'default'@'%';
