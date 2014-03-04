<?php
//5ive definition
$tablename = 'tbl_forum_default_ratings';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'rating_description' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => 1
		),
    'rating_point' => array(
		'type' => 'integer',
		'length' => 4,
        'default' => 0
		)
    );

?>