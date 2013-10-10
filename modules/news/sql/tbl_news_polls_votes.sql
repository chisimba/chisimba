<?php

// Table Name
$tablename = 'tbl_news_polls_votes';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the casted voted in the various polls', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'vote' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'ipaddress' => array(
		'type' => 'text',
		'length' => 20
    ),
	'datecreated' => array(
		'type' => 'timestamp'
    ),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_polls_votes_idx';

$indexes = array(
                'fields' => array(
                	'vote' => array(),
                	'ipaddress' => array(),
                )
        );
		



?>