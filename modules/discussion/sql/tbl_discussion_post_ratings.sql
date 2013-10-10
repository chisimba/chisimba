<?php

//5ive definition
$tablename = 'tbl_discussion_post_ratings';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'post_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'rating' => array(
		'type' => 'text',
		'length' => 32
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
		)
	);
    
//create other indexes here...

$name = 'tbl_discussion_post_ratings_idx';

$indexes = array(
                'fields' => array(
                	'post_id' => array(),
                    'rating' => array(),
                    'userid' => array()
                )
        );
?>