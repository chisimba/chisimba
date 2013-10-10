<?php
// Table Name
$tablename = 'tbl_karmapoints';

//Options line for comments, encoding and character set
$options = array('comment' => 'Karma Points', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'contribution' => array(
		'type' => 'text',
		'length' => 255,
		),
	'points' => array(
		'type' => 'integer',
		'length' => 4,
		)
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>