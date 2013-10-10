<?php

//5ive definition
$tablename = 'tbl_forum_post_text';

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
		'length' => 32,
        'notnull' => 1
		),
    'post_title' => array(
		'type' => 'text',
		'length' => 160,
        'notnull' => 1
		),
    'post_text' => array(
		'type' => 'text',
        'notnull' => 1
		),
    'language' => array(
		'type' => 'text',
		'length' => 2,
        'notnull' => TRUE,
        'default' => 'EN'
		),
    'original_post' => array(
		'type' => 'text',
		'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'readability' => array(
		'type' => 'text',
		'length' => 30
		),
    'wordcount' => array(
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
		)
	);
    
//create other indexes here...

$name = 'tbl_forum_post_text_idx';

$indexes = array(
                'fields' => array(
                	'post_id' => array()
                )
        );
?>