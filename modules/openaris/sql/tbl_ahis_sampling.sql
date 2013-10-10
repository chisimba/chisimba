<?php

$tablename = 'tbl_ahis_sampling';

$options = array('comment'=> 'table to store sampling details','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		
		'newherdid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		 
		 'sampledate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
		
		
		 'number' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
		
		'sentdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
		
		'recievddate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
		 
    );
//create other indexes here...

$name = 'index_tbl_ahis_sampling';

?>