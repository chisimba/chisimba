<?php
// Table Name
$tablename = 'tbl_geo_geoname';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geoname database', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'geonameid' => array(
		'type' => 'integer',
		),
	'name' => array(
		'type' => 'text',
		'length' => 200,
		),
	'asciiname' => array(
		'type' => 'text',
		'length' => 200,
		),
	'alternatenames' => array(
		'type' => 'clob',
		),
	'latitude' => array(
		'type' => 'clob',
		),
	'longitude' => array(
		'type' => 'clob',
		),
	'fclass' => array(
		'type' => 'text',
		'length' => 10,
		),
	'fcode' => array(
		'type' => 'text',
		'length' => 10,
		),
	'country' => array(
		'type' => 'text',
		'length' => 10,
		),
	'cc2' => array(
		'type' => 'text',
		'length' => 60,
		),
	'admin1' => array(
		'type' => 'text',
		'length' => 20,
		),
	'admin2' => array(
		'type' => 'text',
		'length' => 80,
		),
    'admin3' => array(
		'type' => 'text',
		'length' => 20,
		),
	'admin4' => array(
		'type' => 'text',
		'length' => 20,
		),
	'population' => array(
		'type' => 'text',
		'length' => 80,
		),	
	'elevation' => array(
		'type' => 'text',
		'length' => 80,
		),	
	'gtopo30' => array(
		'type' => 'text',
		'length' => 80,
		),	
	'timezone' => array(
		'type' => 'text',
		'length' => 80,
		),	
	'moddate' => array(
		'type' => 'text',
		'length' => 80,
		),
	);
//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>
