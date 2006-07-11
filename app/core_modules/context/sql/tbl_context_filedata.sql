<?
/*
  $sqldata[]="CREATE TABLE tbl_context_filedata (
 id VARCHAR(32) NOT NULL,
  tbl_context_file_tbl_context_parentnodes_id VARCHAR(32) NOT NULL,
  tbl_context_file_id VARCHAR(32) NOT NULL,
  filedata BLOB NULL,
  segment INTEGER NULL,  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  INDEX tbl_context_filedata_FKIndex1(tbl_context_file_id, tbl_context_file_tbl_context_parentnodes_id),
  FOREIGN KEY(tbl_context_file_id, tbl_context_file_tbl_context_parentnodes_id)
    REFERENCES tbl_context_file(id, tbl_context_parentnodes_id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;

";*/


$tablename = 'tbl_context_filedata';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'tbl_context_parentnodes_id' => array(
		'type' => 'text',
		'length' => 64
		),
	'datatype' => array(
		'type' => 'text',
		'length' => 60
		),
    );
?>