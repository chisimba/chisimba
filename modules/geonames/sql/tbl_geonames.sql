<?php
// Table Name
$tablename = 'tbl_geonames';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo name database', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
		'type' => 'clob',
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
	'featureclass' => array(
		'type' => 'text',
		'length' => 10,
		),
	'featurecode' => array(
		'type' => 'text',
		'length' => 10,
		),
	'countrycode' => array(
		'type' => 'text',
		'length' => 10,
		),
	'cc2' => array(
		'type' => 'text',
		'length' => 60,
		),
	'admin1code' => array(
		'type' => 'text',
		'length' => 20,
		),
	'admin2code' => array(
		'type' => 'text',
		'length' => 80,
		),
	'admin1name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'admin2name' => array(
		'type' => 'text',
		'length' => 255,
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
	'timezoneid' => array(
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