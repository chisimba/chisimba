<?php
// Table Name
$tablename = 'tbl_geo_admin1codesascii';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo admin 1st level codes ascii', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
		'length' => 60,
		),
	'name' => array(
		'type' => 'clob',
		),	
	'nameascii' => array(
		'type' => 'clob',
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
