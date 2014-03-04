<?php
// Table Name
$tablename = 'tbl_feedback_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'dfx comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'fb_question' => array(
		'type' => 'clob'
        ),
	'modified' => array(
		'type' => 'timestamp'
		)
	);

//create other indexes here...

?>
