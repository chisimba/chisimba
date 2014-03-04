<?php

//5ive definition
$tablename = 'tbl_poll';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the configuration settings for the polls, links to context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'context_code' => array(
		'type' => 'text',
		'length' => 255
		),
	'cycle_rate' => array(
		'type' => 'text',
		'length' => 25
		),
	'is_repeated' => array(
		'type' => 'integer',
		'length' => 2
		),
	'randomise' => array(
		'type' => 'integer',
		'length' => 2
		),
    'active_date' => array(
		'type' => 'date'
		),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifier_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'poll_index';

$indexes = array(
                'fields' => array(
                	'context_code' => array()
                )
        );
?>