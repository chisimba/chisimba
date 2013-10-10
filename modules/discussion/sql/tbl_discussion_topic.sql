<?php

//5ive definition
$tablename = 'tbl_discussion_topic';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'discussion_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'type_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'topic_tangent_parent' => array(
		'type' => 'text',
		'length' => 32
		),
    'lft' => array(
		'type' => 'integer',
		'length' => 11
		),
    'rght' => array(
		'type' => 'integer',
		'length' => 11
		),
    'first_post' => array(
		'type' => 'text',
		'length' => 32
		),
    'last_post' => array(
		'type' => 'text',
		'length' => 32
		),
    'views' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => 1,
        'default' => 0
		),
    'replies' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => 1,
        'default' => 0
		),
    'status' => array(
		'type' => 'text',
		'length' => 5,
        'notnull' => TRUE,
        'default' => 'OPEN'
		),
    'lockreason' => array(
		'type' => 'text'
		),
    'lockuser' => array(
		'type' => 'text',
		'length' => 25
		),
    'lockdate' => array(
		'type' => 'date'
		),
    'locktime' => array(
		'type' => 'date'
		),
    'userid' => array(
		'type' => 'text',
        'length' => '25',
        'notnull' => 1
		),
    'sticky' => array(
		'type' => 'text',
		'length' => 1,
        'default' => '0'
		),
    'datelastupdated' => array(
		'type' => 'timestamp'
		)
	);

//create other indexes here...

$name = 'tbl_discussion_topic_idx';

$indexes = array(
                'fields' => array(
                	'discussion_id' => array(),
                    'type_id' => array(),
                    'sticky' => array()
                )
        );
?>