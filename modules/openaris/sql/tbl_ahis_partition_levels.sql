<?php

$tablename = 'tbl_ahis_partition_levels';

$options = array('comment'=> 'table to store partition levels','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'partitioncategoryid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
      'partitionlevel' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
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