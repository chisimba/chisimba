<?php

$tablename = 'tbl_ahis_country_languages';

$options = array('comment'=> 'table to store country languages','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'countryid' => array(
		'type' => 'int',
		'length' => 32,
        'notnull' => TRUE
		),
      'languageid' => array(
		'type' => 'int',
		'length' => 32,
        'notnull' => TRUE
		),
	'startdate' => array(
		'type' => 'text',
        'notnull' => TRUE
		),
	'enddate' => array(
		'type' => 'text',
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