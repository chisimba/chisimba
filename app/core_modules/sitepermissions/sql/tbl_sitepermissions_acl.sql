<?php
// Table Name
$tablename = 'tbl_sitepermissions_acl';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep a list of access control lists.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
    'name' => array(
        'type' => 'text',
        'length' => 50,
    ),
	'description' => array(
		'type' => 'text',
		'length' => 255,
	)
);
?>