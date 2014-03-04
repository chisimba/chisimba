<?php
//5ive definition
$tablename = 'tbl_discussion_topic_read';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'topic_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'post_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'userId' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => 1
		),
    'datelastupdated' => array(
		'type' => 'timestamp'
		)
    );

?>