<?php

//5ive definition
$tablename = 'tbl_forum_post';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'post_parent' => array(
		'type' => 'text',
		'length' => 32
		),
    'post_tangent_parent' => array(
		'type' => 'text',
		'length' => 32
		),
    'topic_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'post_order' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		),
    'lft' => array(
		'type' => 'integer',
		'length' => 11
		),
    'rght' => array(
		'type' => 'integer',
		'length' => 11
		),
    'average_ratings' => array(
		'type' => 'text',
		'length' => 32
		),
    'level' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		),
    'userid' => array(
		'type' => 'text',
        'length' => '25',
        'notnull' => 1
		),
    'datecreated' => array(
		'type' => 'timestamp'
		),
    'modifierid' => array(
		'type' => 'text',
        'length' => '25',
        'notnull' => 1
		),
    'datelastupdated' => array(
		'type' => 'timestamp'
		),
    'post_emailed' => array(
		'type' => 'text',
		'length' => 1,
        'default' => 'N'
		)
	);
    
//create other indexes here...

$name = 'tbl_forum_post_idx';

$indexes = array(
                'fields' => array(
                	'topic_id' => array(),
                    'post_parent' => array()
                )
        );
?>