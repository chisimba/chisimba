<?php

$tablename = 'tbl_ahis_diseaselocality';

$options = array('comment'=> 'table to store disease locality data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'outbreakcode' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'localitytypeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
    'latitude' => array(
		'type' => 'float',
		'notnull' => TRUE
		),
	'latdirection' => array(
		'type' => 'text',
        'length' => 1,
		'notnull' => TRUE
		),
	'longitude' => array(
		'type' => 'float',
		'notnull' => TRUE
		),
	'longdirection' => array(
		'type' => 'text',
        'length' => 1,
		'notnull' => TRUE
		),
	'farmingsystemid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
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
	'date_modified' => array (
		'type' => 'date'
        ),	
	'modified_by' => array (
		'type' => 'text',
		'length' => 32
	)    
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_diseaselocality';

?>