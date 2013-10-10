<?php
// Table Name
$tablename = 'tbl_geo_continentcodes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo continent codes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'code' => array(
		'type' => 'text',
		'length' => 10,
		),
	'name' => array(
		'type' => 'text',
		'length' => 20,
		),	
	'geonameid' => array(
		'type' => 'integer',
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
