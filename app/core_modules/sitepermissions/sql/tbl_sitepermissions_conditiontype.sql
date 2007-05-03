<?php
// Table Name
$tablename = 'tbl_sitepermissions_conditiontype';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to store condition type as used by the module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
	),
	'name' => array(
		'type' => 'text',
		'length' => 50,
    ),
);
?>