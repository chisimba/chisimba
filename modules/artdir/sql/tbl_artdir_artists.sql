<?php
// Table Name
$tablename = 'tbl_artdir_artists';

//Options line for comments, encoding and character set
$options = array('comment' => 'artdir artists', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'actname' => array(
		'type' => 'text',
		'length' => 255,
		),
	'description' => array(
		'type' => 'text',
		'length' => 255,
		),
	'catid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'contactperson' => array(
		'type' => 'text',
		'length' => 255,
		),
	'contactnum' => array(
		'type' => 'text',
		'length' => 255,
		),
	'altnum' => array(
		'type' => 'text',
		'length' => 255,
		),
	'email' => array(
		'type' => 'text',
		'length' => 255,
		),
	'website' => array(
		'type' => 'text',
		'length' => 255,
		),
	'bio' => array(
		'type' => 'clob',
		),
    'thumbnail' => array(
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
