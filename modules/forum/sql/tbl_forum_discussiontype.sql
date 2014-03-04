<?php
//5ive definition
$tablename = 'tbl_forum_discussiontype';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'type_name' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => 1
		),
    'type_icon' => array(
		'type' => 'text',
		'length' => 50
		),
    'status' => array(
		'type' => 'text',
		'length' => 1,
        'default' => 0
		)
    );

?>