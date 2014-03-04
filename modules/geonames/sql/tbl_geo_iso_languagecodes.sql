<?php
// Table Name
$tablename = 'tbl_geo_iso_languagecodes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo iso lang codes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'iso_639_3' => array(
		'type' => 'text',
		'length' => 10
		),	
	'iso_639_2' => array(
		'type' => 'text',
		'length' => 50
		),	
	'iso_639_1' => array(
		'type' => 'text',
		'length' => 50
		),	
	'language_name' => array(
		'type' => 'text',
		'length' => 200
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
