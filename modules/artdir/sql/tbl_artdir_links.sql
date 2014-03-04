<?php
// Table Name
$tablename = 'tbl_artdir_links';

//Options line for comments, encoding and character set
$options = array('comment' => 'artdir artist links', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'artistid' => array(
		'type' => 'text',
		'length' => 255,
		),
    'catid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'linkname' => array(
		'type' => 'text',
		'length' => 255,
		),
	'link' => array(
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
