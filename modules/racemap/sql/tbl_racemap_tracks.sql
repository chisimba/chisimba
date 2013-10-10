<?php
// Table Name
$tablename = 'tbl_racemap_tracks';

//Options line for comments, encoding and character set
$options = array('comment' => 'Racemap tracks data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'metaid' => array(
        'type' => 'text', 
        'length' => 32,
        ),
	'lat' => array(
		'type' => 'text',
		'length' => 255,
		),
	'lon' => array(
		'type' => 'text',
		'length' => 255,
		),
    'elevation' => array(
		'type' => 'text',
		'length' => 255,
		),
	'speed' => array(
		'type' => 'text',
		'length' => 255,
		),
	'course' => array(
		'type' => 'text',
		'length' => 255,
		),
	'description' => array(
		'type' => 'text',
		'length' => 255,
		),
	'creationtime' => array(
		'type' => 'timestamp',
		),
	'segname' => array(
		'type' => 'text',
		'length' => 255,
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
