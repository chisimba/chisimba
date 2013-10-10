<?php
// Table Name
$tablename = 'tbl_news_albums';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the news albums', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'albumname' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
	'albumstatus' => array (
		'type' => 'text',
		'length' => 5,
		'notnull' => 1,
		'default' => 'draft'
	),
	'albumdescription' => array (
		'type' => 'text',
		'length' => 255
	),
	'albumdate' => array (
		'type' => 'date',
		'notnull' => 1
	),
	'albumlocation' => array(
		'type' => 'text',
		'length' => 32
    ),
	'albumdefaultimage' => array(
		'type' => 'text',
		'length' => 32
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
$name = 'tbl_news_albums_idx';

$indexes = array(
                'fields' => array(
                	'albumname' => array(),
                	'albumdate' => array(),
                	'albumlocation' => array(),
                	'albumdefaultimage' => array(),
                	'creatorid' => array(),
                	'datecreated' => array(),
                )
        );
		



?>