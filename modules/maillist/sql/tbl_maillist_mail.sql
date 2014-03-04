<?php

// Table Name
$tablename = 'tbl_maillist_mail';

//Options line for comments, encoding and character set
$options = array('comment' => 'Mailing list temp table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'body' => array(
		'type' => 'clob',
		),
	'subject' => array(
		'type' => 'text',
		'length' => 255
		),
	'sender' => array(
		'type' => 'text',
		'length' => 255
		),
	'fileid' => array(
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