<?php

// Table Name
$tablename = 'tbl_news_albumkeywords';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the keywords for photo albums', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'albumid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'keyword' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_albumkeywords_idx';

$indexes = array(
                'fields' => array(
                	'albumid' => array(),
                	'keyword' => array(),
                )
        );
		



?>