<?php

//5ive definition
$tablename = 'tbl_hivaids_videos';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store a list of videos for general download', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'file_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'file_name' => array(
		'type' => 'text',
		'length' => 150
		),
	'description' => array(
		'type' => 'clob'
		),
	'creatorid' => array(
	   'type' => 'text',
		'length' => 32
		),
	'modifierid' => array(
	   'type' => 'text',
		'length' => 32
		),
    'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'hivaids_videos_index';

$indexes = array(
                'fields' => array(
                	'file_id' => array()
                )
        );
?>