<?php
// Table Name
$tablename = 'tbl_beatstream';

//Options line for comments, encoding and character set
$options = array('comment' => 'beatstream table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'suggestion' => array(
        'type' => 'text', 
        'length' => 255,
        ),
	'votes_up' => array(
		'type' => 'integer',
		'length' => 6,
		'default' => 0,
		),
    'votes_down' => array(
		'type' => 'integer',
		'length' => 6,
		'default' => 0,
		),
	'rating' => array(
		'type' => 'integer',
		'length' => 6,
		'default' => 0,
		),
	'dt' => array(
        'type' => 'timestamp',
       ),
	);
	
//create other indexes here...

$name = 'id';

$indexes = array(
                'fields' => array(
                	'rating' => array(),
                )
        );
?>
