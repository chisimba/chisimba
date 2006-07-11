<?php
/*
  $sqldata[] = "CREATE TABLE tbl_permissions_acl_description (
  id VARCHAR(32) NOT NULL,
  name VARCHAR(100) UNIQUE,
  description VARCHAR(100),
  
  last_updated DATETIME NOT NULL,
  last_updated_by VARCHAR(32) NULL,
  
  PRIMARY KEY (id)
) TYPE=InnoDB COMMENT='This table stores access control list acl description for debugig purposes.'";

*/

// Table Name
$tablename = 'tbl_permissions_acl_description';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores access control list acl description for debugging purposes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 100
		),
    'description' => array(
		'type' => 'text',
		'length' => 100
		),
    'last_updated' => array(
		'type' => 'datetime',
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

?>
