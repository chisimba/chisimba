<?php

$tablename = 'tbl_ahis_partitions';

$options = array('comment'=> 'table to store partitions','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'countryid' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'partitionlevelid' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'partitioncode' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
	'partitionname' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
	'parentpartition' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => FALSE
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