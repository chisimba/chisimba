<?php

$tablename = 'tbl_ahis_newherd';

$options = array('comment'=> 'table to store newherd','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'activeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'territory' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'geolevel2' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'latdeg' => array(
		'type' => 'integer',
		'length' => 4,
        'notnull' => TRUE,
		'default' => 0
		),
	'latmin' => array(
		'type' => 'float',
        'notnull' => TRUE,
		'default' => 0
		),
	'latdirec' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
		'default' => 'E'
		),
	'longdeg' => array(
		'type' => 'integer',
		'length' => 4,
        'notnull' => TRUE,
		'default' => 0
		),
	'longmin' => array(
		'type' => 'float',
        'notnull' => TRUE,
		'default' => 0
		),
	'longdirec' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
		'default' => 'N'
		),
	'farmname' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	'farmingtype' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_newherd';

?>