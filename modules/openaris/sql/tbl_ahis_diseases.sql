<?php

$tablename = 'tbl_ahis_diseases';

$options = array('comment'=> 'table to store disease types','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
	'disease_code' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
    
    'scientific_name' => array(
		'type' => 'text',
		'length' => 200,
        'notnull' => TRUE
		),
        
    'disease_name' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'short_name' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'reference_code' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'in_OIE_list' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'zoonotic' => array(
		'type' => 'text',
		'length' => 20,
        'notnull' => TRUE
		),
	'description' => array (
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
	),
	'has_vaccine' => array (
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
	),
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_diseases';

?>