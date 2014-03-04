<?php
// Table Name
$tablename = 'tbl_examiners_departments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store faculty departments.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'fac_id' => array(
	   'type' => 'text',
	   'length' => '32',
    ),
	'department_name' => array(
	   'type' => 'text',
	   'length' => '255',
    ),
	'deleted' => array(
		'type' => 'integer',
		'length' => '1',
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>