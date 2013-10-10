<?php

//table definition for tbl_podcast_context
//author: Mohamed Yusuf
$tablename = 'tbl_podcast_context';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'podcastid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'contextcode' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => 1
		)
	);
	
//create other indexes here...

$name = 'tbl_podcast_context_idx';

$indexes = array(
                'fields' => array(
                	'podcastId' => array(),
                    'contextcode' => array()
                )
        );
?>