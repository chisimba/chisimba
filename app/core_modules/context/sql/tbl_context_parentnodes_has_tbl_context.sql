<?php
/*
  $sqldata[]="CREATE TABLE tbl_context_parentnodes_has_tbl_context (
  tbl_context_contextCode VARCHAR(255) NOT NULL,
  tbl_context_id VARCHAR(32) NOT NULL,
  id VARCHAR(32) NULL, 
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(tbl_context_contextCode, tbl_context_id),
  INDEX tbl_context_has_tbl_context_parentnodes_FKIndex1(tbl_context_id, tbl_context_contextCode),
  FOREIGN KEY(tbl_context_id, tbl_context_contextCode)
    REFERENCES tbl_context(id, contextCode)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_parentnodes_has_tbl_context';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'tbl_context_contextCode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'tbl_context_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_context_has_tbl_context_parentnodes_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_context_id' => array(), 
                    'tbl_context_contextCode' => array()
                )
        );
?>