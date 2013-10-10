<?php
//5ive definition
$tablename = 'tbl_forum_subscribe_forum';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'userid' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => 1
		),
    'forum_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'internal' => array(
		'type' => 'text',
        'length' => '1',
        'notnull' => 1,
        'default' => 'N'
		),
    'external' => array(
		'type' => 'text',
        'length' => '1',
        'notnull' => 1,
        'default' => 'N'
		),
    'datecreated' => array(
		'type' => 'timestamp'
		)
    );
    
//create other indexes here...

$name = 'tbl_forum_subscribe_forum_idx';

$indexes = array(
                'fields' => array(
                	'forum_id' => array(),
                    'userid' => array()
                )
        );

?>