<?php
// Table Name
$tablename = 'tbl_ahis_animal_population';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains animal population data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'classification' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'number' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'production' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'reportdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'source' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	)	

);
//create other indexes here...

$name = 'index_tbl_ahis_animal_population';

?>