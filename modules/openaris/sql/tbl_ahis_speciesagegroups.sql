<?php

$tablename = 'tbl_ahis_speciesagegroups';

$options = array('comment'=> 'table to store species age groups','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
    'species_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'agegroup' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'abbreviation' => array(
		'type' => 'text',
		'length' => 10,
        'notnull' => TRUE
		),
	'description' => array(
		'type' => 'clob',
		),
	 'lowerlimit' => array(
		'type' => 'integer',
		'length' => 5,
        ),
	 'upperlimit' => array(
		'type' => 'integer',
		'length' => 5,
        ),
	'start_date' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'end_date' => array(
		'type' => 'date',
        'notnull' => FALSE
		),
    'date_created' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'date_modified' => array(
		'type' => 'date',
        ),
	'modified_by' => array(
		'type' => 'text',
		'length' => 32,
        ),
    );
//create other indexes here...



?>