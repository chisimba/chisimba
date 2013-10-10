<?php
// Table Name
$tablename = 'tbl_photostack_album';

//Options line for comments, encoding and character set
$options = array('comment' => 'photostack albums', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'albumname' => array(
		'type' => 'text',
		'length' => 255,
		),
    'description' => array(
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
