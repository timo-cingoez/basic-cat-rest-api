# A basic implementation of a REST-API for cats.

## SQL setup
'''create database cat_db;
use cat_db;
create table cat (
id INT NOT NULL AUTO_INCREMENT,
name VARCHAR(128) NOT NULL,
age INT NOT NULL DEFAULT 0,
is_happy BOOLEAN NOT NULL DEFAULT TRUE,
PRIMARY KEY (id));'''