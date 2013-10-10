<?php

// Table Name
$tablename = 'tbl_news_polls';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the website polls', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'pollquestion' => array(
		'type' => 'text',
		'notnull' => 1
    ),
	'enddate' => array(
		'type' => 'date',
    ),
	'endtime' => array(
		'type' => 'time'
    ),
	'pollactive' => array(
		'type' => 'text',
		'length' => 1,
		'default' => 'N',
		'notnull' => 1
    ),
	'datecreated' => array(
		'type' => 'timestamp'
    ),
);



?>