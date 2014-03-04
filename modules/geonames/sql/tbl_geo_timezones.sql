<?php
// Table Name
$tablename = 'tbl_geo_timezones';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo timezones', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'timezoneid' => array(
		'type' => 'text',
		'length' => 200,
		),
	'gmt_offset' => array(
		'type' => 'text',
		'length' => 100,
		),	
	'dst_offset' => array(
		'type' => 'text',
		'length' => 100
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
