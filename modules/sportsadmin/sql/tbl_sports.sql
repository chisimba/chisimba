<?php

  $sqldata[]="CREATE TABLE tbl_sports (
  id varchar(32) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  userId varchar(50) NOT NULL default '',
  description text default '',
  evaluationMode varchar(100) default 'Goals', 
  dateCreated timestamp(14),  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (userId)
  REFERENCES tbl_users(userId) 
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
  
)TYPE=InnoDB
";


$sqldata[]=" INSERT INTO `tbl_sports` (id,name,userId,dateCreated) values ('gen13Srv39Nme19_1','Soccer','1',now());";
$sqldata[]=" INSERT INTO `tbl_sports` (id,name,userId,dateCreated) values ('gen13Srv39Nme19_2','Volleball','1',now());";


?>