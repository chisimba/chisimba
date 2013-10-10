<?php
// Table Name
$tablename = 'tbl_tweetlic_streams';

//Options line for comments, encoding and character set
$options = array('comment' => 'licensed tweet streams', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'screen_name' => array(
        'type' => 'text', 
        'length' => 255,
        ),
	'copyright' => array(
		'type' => 'text',
		'length' => 255,
		),
	'creationtime' => array(
        'type' => 'timestamp',
       ),
	);
//create other indexes here...

$name = 'screen_name';

$indexes = array(
                'fields' => array(
                	'screen_name' => array(),
                )
        );
?>
