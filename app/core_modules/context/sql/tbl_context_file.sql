<?
  $sqldata[]="CREATE TABLE tbl_context_file (
  id VARCHAR(32) NOT NULL,
  tbl_context_parentnodes_id VARCHAR(32) NOT NULL,
  datatype VARCHAR(60) NULL,
  title VARCHAR(120) NULL,
  description VARCHAR(255) NULL,
  version VARCHAR(60) NULL,
  name VARCHAR(120) NULL,
  size BIGINT(20) NULL,
  filedate DATETIME NULL,
  path VARCHAR(255) NULL,  
  category varchar(32) default NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id, tbl_context_parentnodes_id),
  INDEX tbl_context_file_FKIndex1(tbl_context_parentnodes_id),
  FOREIGN KEY(tbl_context_parentnodes_id)
    REFERENCES tbl_context_parentnodes(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";
?>