<?
  $sqldata[]="CREATE TABLE tbl_context_sharednodes (
  id VARCHAR(32) NOT NULL,
  shared_nodeid VARCHAR(32) NULL,
  root_nodeid VARCHAR(32) NOT NULL,
  nodeid VARCHAR(32) NULL, 
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id)
)
TYPE=InnoDB;
";
?>