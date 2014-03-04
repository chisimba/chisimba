<?php

$tablename = 'tbl_ahis_farmingsystems';

$options = array('comment'=> 'table to store farming systems','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'farmingsystem' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'abbreviation' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
	'description' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
	'startdate' => array(
		'type' => 'text',
        'notnull' => TRUE
		),
	'enddate' => array(
		'type' => 'text',
        'notnull' => FALSE
		),
    'createdon' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'createdby' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'modifiedon' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'modifiedby' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    );
//create other indexes here...



?>