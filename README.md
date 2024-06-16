# A basic implementation of a REST-API in plain PHP (for cats).

## SQL setup
``` sql
create database cat_db;
use cat_db;
create table cat
(
    id       INT          NOT NULL AUTO_INCREMENT,
    name     VARCHAR(128) NOT NULL,
    age      INT          NOT NULL DEFAULT 0,
    is_happy BOOLEAN      NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id)
);
```

## Basic testing
``` cURL
curl http://localhost/basic-cat-rest-api/cats
curl http://localhost/basic-cat-rest-api/cats/1
```
