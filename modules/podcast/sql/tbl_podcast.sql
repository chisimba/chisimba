<?php

//5ive definition
$tablename = 'tbl_podcast';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'fileid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'title' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => 1
		),
    'description' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => 1
		),
	'creatorid' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'modifierid' => array(
		'type' => 'text',
        'length' => 25
		),
	'datemodified' => array(
		'type' => 'timestamp'
		),
	);
	
//create other indexes here...

$name = 'tbl_podcast_idx';

$indexes = array(
                'fields' => array(
                	'fileid' => array(),
                    'creatorid' => array(),
                    'datecreated' => array(),
                    'modifierid' => array(),
                    'datemodified' => array()
                )
        );
?>