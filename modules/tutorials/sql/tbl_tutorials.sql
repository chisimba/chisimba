<?php
// Table Name
$tablename = 'tbl_tutorials';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold tutorials', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255,
	),
	'name' => array(
		'type' => 'text',
		'length' => 255,
    ),
	'tutorial_type' => array(
		'type' => 'integer',
		'length' => '1',
	),
    'description' => array(
        'type' => 'clob',
    ),
    'percentage' => array(
        'type' => 'float',
    ),
    'total_mark' => array(
        'type' => 'integer',
        'length' => '3',
    ),
    'answer_open' => array(
        'type' => 'timestamp',
    ),
    'answer_close' => array(
        'type' => 'timestamp',
    ),
    'marking_open' => array(
        'type' => 'timestamp',
    ),
    'marking_close' => array(
        'type' => 'timestamp',
    ),
    'moderation_open' => array(
        'type' => 'timestamp',
    ),
    'moderation_close' => array(
        'type' => 'timestamp',
    ),
    'penalty' => array(
        'type' => 'float',
    ),
	'deleted' => array( // active, deleted
		'type' => 'integer', 
		'length' => 1,
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>