<?php
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
		'type' => 'date',
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

?>