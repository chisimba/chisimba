<?php
/*
  $sqldata[]="CREATE TABLE tbl_context_nodes (
  id VARCHAR(32) NOT NULL,
  tbl_context_parentnodes_id VARCHAR(32) NOT NULL,
  parent_Node VARCHAR(32)  NULL,  
  prev_Node VARCHAR(32) NULL,
  next_Node VARCHAR(32) NULL, 
  title VARCHAR(255) NULL,
  script TEXT null,
  sortindex INT DEFAULT 1,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  INDEX tbl_context_nodes_FKIndex1(tbl_context_parentnodes_id),
  FOREIGN KEY(tbl_context_parentnodes_id)
    REFERENCES tbl_context_parentnodes(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_nodes';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'tbl_context_parentnodes_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'parent_Node' => array(
        'type' => 'text',
        'length' => 32
        ),
    'prev_Node' => array(
        'type' => 'text',
        'length' => 32
        ),
    'next_Node' => array(
        'type' => 'text',
        'length' => '32'
        ),
    'title' => array(
        'type' => 'text',
        'length' => 255
        ),
    'script' => array(
        'type' => 'text'
        ),
    'sortindex' => array(
        'type' => 'integer',
        'default' => 1
        ),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_context_nodes_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_context_parentnodes_id' => array()
                )
        );
?>