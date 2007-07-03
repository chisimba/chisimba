<?php
// Table Name
$tablename = 'tbl_sitepermissions_acl_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep a list of users for each acl.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
    'aclid' => array(
        'type' => 'text',
        'length' => 32,
    ),
	'type' => array( // group, user
		'type' => 'integer',
		'length' => 1,
	),
	'typeid' => array(
		'type' => 'text',
		'length' => 32,
	),
);
//create other indexes here...

$name = 'FK_acl_users';

$indexes = array(
    'fields' => array(
        'typeid' => array(),
    )
);
?>