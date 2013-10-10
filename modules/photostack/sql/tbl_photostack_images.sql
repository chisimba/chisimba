<?php
// Table Name
$tablename = 'tbl_photostack_images';

//Options line for comments, encoding and character set
$options = array('comment' => 'photostack album images', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'albumid' => array(
		'type' => 'text',
		'length' => 255,
		),
    'imageid' => array(
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
