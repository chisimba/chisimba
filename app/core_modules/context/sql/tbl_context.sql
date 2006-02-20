<?
  $sqldata[]="CREATE TABLE tbl_context (
  id VARCHAR(32) NOT NULL,
  contextCode VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  menutext VARCHAR(255) NULL,
  about TEXT,
  userid VARCHAR(255) NOT NULL,
  dateCreated DATE NULL,  
  isClosed INT NULL,
  isActive INT NULL,
  isPublic INT NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id, contextCode)
)
TYPE=InnoDB; COMMENT='Context Information';
";

$sqldata[]="ALTER TABLE `tbl_context` ADD INDEX `contextCode` ( `contextCode` ) ";
?>