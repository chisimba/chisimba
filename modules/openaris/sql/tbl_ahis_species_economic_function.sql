<?php

$tablename = 'tbl_ahis_species_economic_function';

$options = array('comment'=> 'table to store species economic function','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'species_id' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'economic_function' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'abbreviation' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'description' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'start_date' => array (
		'type' => 'date',
        'notnull' =>0
	),
	'end_date' => array (
		'type' => 'date',
        'notnull' =>0
	),
	'date_created' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 0
		),
	'date_modified' => array (
		'type' => 'date',
        'notnull' =>0
	),	
	'modified_by' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 0
	)
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_species_economic_function';

?>