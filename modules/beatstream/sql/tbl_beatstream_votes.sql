<?php
// Table Name
$tablename = 'tbl_beatstream_votes';

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
	'suggestion_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'ip' => array(
        'type' => 'integer', 
        'length' => 10,
        ),
	'day' => array(
		'type' => 'date',
		),
    'vote' => array(
		'type' => 'integer',
		'length' => 1,
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
                	'ip' => array(),
                )
        );
?>
