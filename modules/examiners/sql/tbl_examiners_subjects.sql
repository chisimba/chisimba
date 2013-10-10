<?php
// Table Name
$tablename = 'tbl_examiners_subjects';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store individual subjects to be examined.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'dep_id' => array(
	   'type' => 'text',
	   'length' => '32',
    ),
	'course_code' => array(
	   'type' => 'text',
	   'length' => '255',
    ),
	'course_name' => array(
	   'type' => 'text',
	   'length' => '255',
    ),
	'course_level' => array(
		'type' => 'integer',
		'length' => '1',
	),
	'course_status' => array(
		'type' => 'integer',
		'length' => '1',
	),
	'last_active_year' => array(
		'type' => 'integer',
		'length' => '4',
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