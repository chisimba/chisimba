<?php

$tablename = 'tbl_ahis_active_surveillance';

$options = array('comment'=> 'table to store active surveillance data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'reporterid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'campname' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'surveytype' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'disease' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'sensitivity' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'specificity' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'testtype' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'comments' => array(
		'type' => 'clob',
		 'notnull' => FALSE 
		)
    );
//create other indexes here...

$name = 'index_tbl_ahis_active_surveillance';

?>
