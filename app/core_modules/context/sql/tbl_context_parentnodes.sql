<?
  $sqldata[]="CREATE TABLE tbl_context_parentnodes (
  id VARCHAR(32) NOT NULL,
  tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode VARCHAR(255) NOT NULL,
  tbl_context_parentnodes_has_tbl_context_tbl_context_id VARCHAR(32) NOT NULL,
  userId VARCHAR(255) NULL,
  dateCreated DATE NULL,
  datemodified DATE NULL,
  menu_text VARCHAR(255) NULL,
  title VARCHAR(255) NULL,  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  INDEX tbl_context_parentnodes_FKIndex1(tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode, tbl_context_parentnodes_has_tbl_context_tbl_context_id),
  FOREIGN KEY(tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode, tbl_context_parentnodes_has_tbl_context_tbl_context_id)
    REFERENCES tbl_context_parentnodes_has_tbl_context(tbl_context_contextCode, tbl_context_id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";
?>