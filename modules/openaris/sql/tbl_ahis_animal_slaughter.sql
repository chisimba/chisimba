<?php
// Table Name
$tablename = 'tbl_ahis_animal_slaughter';

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
	'num_cattle' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'num_sheep' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'num_goats' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'num_pigs' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'num_poultry' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
		),
	'other' => array (
		'type' => 'clob'
	),
	'name_of_other' => array (
		'type' => 'clob'
	),
	'reportdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'remarks' => array(
		'type' => 'clob'
    )				

);
//create other indexes here...

$name = 'index_tbl_ahis_animal_slaughter';

?>