<?php
// Table Name
$tablename = 'tbl_ahis_countries';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains countries data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'iso_country_code' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' =>1
	),
	'common_name' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'official_name' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'default_language' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'default_currency' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'country_idd' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'north_latitude' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'south_latitude' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'west_longitude' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'east_longitude' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'area' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'unit_of_area_id' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'date_created' => array(
		'type' => 'date',
        'notnull' => 0
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 0
		),
	'date_modified' => array (
		'type' => 'date',
        'notnull' => 0
	),	
	'modified_by' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 0
	)

);
//create other indexes here...

$name = 'index_tbl_ahis_countries';

?>