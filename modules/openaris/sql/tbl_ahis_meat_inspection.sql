<?php
// Table Name
$tablename = 'tbl_ahis_meat_inspection';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains meat inspection data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'district' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' =>1
	),
	'inspection_date' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' =>1
	),
	'num_of_cases' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'reportdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'remarks' => array(
		'type' => 'clob'
    ),
	'num_of_risks' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	)	

);
//create other indexes here...

$name = 'index_tbl_ahis_meat_inspection';


?>