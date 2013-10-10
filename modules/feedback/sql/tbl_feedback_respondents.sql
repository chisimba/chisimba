<?php
// Table Name
$tablename = 'tbl_feedback_respondents';

//Options line for comments, encoding and character set
$options = array('comment' => 'dfx comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 50,
		),
    'email' => array(
		'type' => 'text',
		'length' => 50,
		)
	);

//create other indexes here...

?>
