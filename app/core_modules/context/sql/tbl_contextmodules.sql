<?
  $sqldata[]="CREATE TABLE tbl_contextmodules (
  id varchar(32) NOT NULL default '',
  contextCode varchar(32) NOT NULL default '',
  moduleId varchar(50) NOT NULL default '',  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (id,contextCode,moduleId),
  INDEX tbl_contextmodules_FKIndex1(contextCode),
  INDEX tbl_contextmodules_FKIndex2(moduleId)
  
)TYPE=InnoDB
";
?>