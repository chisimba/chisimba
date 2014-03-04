<?php
// Table Name
$tablename = 'tbl_geo_postalcodes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo postal codes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'countrycode' => array(
		'type' => 'text',
		'length' => 10,
		),
	'postalcode' => array(
		'type' => 'text',
		'length' => 10,
		),	
	'placename' => array(
		'type' => 'text',
		'length' => 180,
		),	
    'admin1name' => array(
		'type' => 'text',
		'length' => 100,
		),		
    'admin1code' => array(
		'type' => 'text',
		'length' => 20,
		),		
    'admin2name' => array(
		'type' => 'text',
		'length' => 100,
		),		
    'admin2code' => array(
		'type' => 'text',
		'length' => 20,
		),		
    'admin3name' => array(
		'type' => 'text',
		'length' => 100,
		),		
    'latitude' => array(
		'type' => 'float',
		),		
    'longitude' => array(
		'type' => 'float',
		),		
    'accuracy' => array(
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
