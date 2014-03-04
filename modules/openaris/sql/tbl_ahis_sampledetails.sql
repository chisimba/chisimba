<?php

$tablename = 'tbl_ahis_sampledetails';

$options = array('comment'=> 'table to store sample details','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		
	'samplingid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	
    'sampleid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		
		
		 'newherdid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		 'animalid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		 'species' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 
		 'age' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'sex' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'sampletype' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'testtype' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'testdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
		'testresult' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'specification' => array(
		'type' => 'clob',
		
		),
		 'vachist' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'number' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		 'remarks' => array(
		'type' => 'clob',
		
		),
		'samplingdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
		
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_sampledetails';

?>