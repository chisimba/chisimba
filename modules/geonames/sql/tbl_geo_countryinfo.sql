<?php
// Table Name
$tablename = 'tbl_geo_countryinfo';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geoname country information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'iso_alpha2' => array(
		'type' => 'text',
		'length' => 10
		),	
	'iso_alpha3' => array(
		'type' => 'text',
		'length' => 10
		),	
	'iso_numeric' => array(
		'type' => 'integer',
		),	
	'fips_code' => array(
		'type' => 'text',
		'length' => 10
		),	
	'country' => array(
		'type' => 'text',
		'length' => 200
		),	
	'capital' => array(
		'type' => 'text',
		'length' => 200
		),	
	'areainsqkm' => array(
		'type' => 'integer',
		),	
	'population' => array(
		'type' => 'integer',
		),	
	'continent' => array(
		'type' => 'text',
		'length' => 10,
		),	
	'tld' => array(
		'type' => 'text',
		'length' => 10,
		),
	'currency_code' => array(
		'type' => 'text',
		'length' => 10,
		),		
	'currency_name' => array(
		'type' => 'text',
		'length' => 15,
		),		
	'phone' => array(
		'type' => 'text',
		'length' => 20,
		),		
	'postal' => array(
		'type' => 'text',
		'length' => 100,
		),		
	'postalregex' => array(
		'type' => 'text',
		'length' => 500,
		),		
	'languages' => array(
		'type' => 'text',
		'length' => 200,
		),	
	'geonameid' => array(
		'type' => 'integer',
		),			
	'neighbours' => array(
		'type' => 'text',
		'length' => 50,
		),		
	'equivalent_fips_code' => array(
		'type' => 'text',
		'length' => 10,
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
