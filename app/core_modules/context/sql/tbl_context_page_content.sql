<?php
  $sqldata[]="CREATE TABLE tbl_context_page_content (
  id VARCHAR(32) NOT NULL,
  tbl_context_nodes_id VARCHAR(32) NOT NULL,
  menu_text VARCHAR(255) NULL,
  body LONGTEXT NULL,
  fullname VARCHAR(255) NULL,
  description MEDIUMTEXT NULL,
  isIndexPage VARCHAR(20) NULL,
  ownerId VARCHAR(255) NULL, 
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  INDEX tbl_context_page_content_FKIndex1(tbl_context_nodes_id),
  FOREIGN KEY(tbl_context_nodes_id)
    REFERENCES tbl_context_nodes(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";

$tablename = 'tbl_context_page_content';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'tbl_context_nodes_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'menu_text' => array(
		'type' => 'text',
		'length' => 255
		),
    'body' => array(
		'type' => 'text'
		),
    'fullname' => array(
		'type' => 'text',
		'length' => 255
		),
	'description' => array(
		'type' => 'text'
		),
    'isIndexPage' => array(
		'type' => 'text',
        'length' => 20
		),
    'ownerId' => array(
		'type' => 'text',
        'length' => 255
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_context_nodes_FKIndex1';

$indexes = array(
                'fields' => array(
                	'tbl_context_nodes_id' => array()
                )
        );
?>