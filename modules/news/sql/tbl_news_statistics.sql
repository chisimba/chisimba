<?php
// Table Name
$tablename = 'tbl_news_statistics';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the statistics of stories. It is in a separate table to prevent updates to the main table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'storyid' => array (
		'type' => 'text',
		'length' =>32,
		'notnull' => 1
	),
	'timesviewed' => array (
		'type' => 'integer',
	),
	'timesemailed' => array (
		'type' => 'integer',
	),
	'timesprinted' => array (
		'type' => 'integer',
	)
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_statistics_idx';

$indexes = array(
                'fields' => array(
                	'storyid' => array(),
                	'timesviewed' => array(),
                	'timesemailed' => array(),
                	'timesprinted' => array(),
                )
        );
		



?>