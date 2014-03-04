<?php

// Table Name
$tablename = 'tbl_maillist_subscribers';

//Options line for comments, encoding and character set
$options = array('comment' => 'Mailing list subscriber information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'modifiedby' => array(
		'type' => 'text',
		'length' => 50
		),
	'datecreated' => array(
		'type' => 'text',
		'length' => 50
		),
	'dateupdated' => array(
		'type' => 'text',
		'length' => 50
		),
	'list' => array(
		'type' => 'text',
		'length' => 255
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