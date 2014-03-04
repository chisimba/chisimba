<?php
// Table Name
$tablename = 'tbl_news_photo';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the news photos grouped into albums', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'fileid' => array (
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
	),
	'albumid' => array (
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
	),
	'caption' => array (
		'type' => 'text',
		'length' => 255
	),
	'license' => array (
		'type' => 'text',
		'length' => 32
	),
	'photoorder' => array (
		'type' => 'integer',
		'length' => 11
	),
	'creatorid' => array (
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
	),
	'datecreated' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_photo_idx';

$indexes = array(
                'fields' => array(
                	'fileid' => array(),
                	'albumid' => array(),
                	'license' => array(),
                	'photoorder' => array(),
                	'creatorid' => array(),
                	'datecreated' => array(),
                )
        );
		



?>