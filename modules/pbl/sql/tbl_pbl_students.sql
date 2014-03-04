<?php
$sqldata[] = "CREATE TABLE tbl_pbl_students( 
    id VARCHAR(32) NOT NULL, 
    name varchar(30), 
    password varchar(15), 
    context varchar(50), 
    regdate datetime, 
    lastlogin timestamp(10), 
    PRIMARY KEY (id)) TYPE=INNODB ";

$sqldata[] = "INSERT INTO tbl_pbl_students (id,name) values('uwc@-1','student')";
?>