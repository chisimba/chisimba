<?php

$tablename = 'tbl_ahis_speciestropicallivestockunit';

$options = array('comment'=> 'table to store species tropical livestock unit','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
    'speciesnameid' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	 'speciescategoryid' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'tlufactor' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
	'remarks' => array(
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