<?php

// Table Name
$tablename = 'tbl_news_polls_options';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the vote options within the different polls', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'pollid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'optionstr' => array(
		'type' => 'text',
		'length' => 100,
		'notnull' => 1
    ),
	'optionorder' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => 1
    ),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_polls_options_idx';

$indexes = array(
                'fields' => array(
                	'pollid' => array(),
                	'optionstr' => array(),
                	'optionorder' => array(),
                )
        );
		



?>