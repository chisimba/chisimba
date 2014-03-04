<?php
/**
*
* A sample SQL file for schoolusers. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_schoolusers_data';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of extra user data for the schoolusers module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'middle_name' => array(
		'type' => 'text',
		'length' => 100,
		),
	'date_of_birth' => array(
		'type' => 'date',
		),
	'address' => array(
		'type' => 'text',
		'length' => 100,
		),
	'city' => array(
		'type' => 'text',
		'length' => 100,
		),
	'state' => array(
		'type' => 'text',
		'length' => 100,
		),
	'postal_code' => array(
		'type' => 'text',
		'length' => 100,
		),
	'school_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'description' => array(
		'type' => 'text',
		'length' => 255,
		),
	'isactive' => array(
		'type' => 'text',
		'length' => 1,
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'modified_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_modified' => array(
		'type' => 'timestamp'
		),
	);

//create other indexes here...

$name = 'tbl_schoolusers_text_idx';

$indexes = array(
    'fields' => array(
         'id' => array(),
         'user_id' => array(),
         'school_id' => array(),
    )
);
?>