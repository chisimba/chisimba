<?php

$tablename = 'tbl_ahis_diseasecontrolmeasure';

$options = array('comment'=> 'table to store disease control measure data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'controlmeasureid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'othermeasureid' => array(
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

$name = 'index_tbl_ahis_diseasecontrolmeasure';

?>