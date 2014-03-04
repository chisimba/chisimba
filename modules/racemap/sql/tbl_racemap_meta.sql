<?php
// Table Name
$tablename = 'tbl_racemap_meta';

//Options line for comments, encoding and character set
$options = array('comment' => 'Racemap metadata', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'name' => array(
        'type' => 'text', 
        'length' => 255,
        ),
	'description' => array(
		'type' => 'clob',
		),
	'author' => array(
		'type' => 'text',
		'length' => 255,
		),
	'copyright' => array(
		'type' => 'text',
		'length' => 255,
		),
	'link' => array(
		'type' => 'text',
		'length' => 255,
		),
    'creationtime' => array(
        'type' => 'timestamp',
       ),
    'keywords' => array(
		'type' => 'clob',
		),
	'bounds' => array(
		'type' => 'clob',
		),
	'extensions' => array(
		'type' => 'clob',
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
