<?php
// Table Name
$tablename = 'tbl_livechat_messages';

//Options line for comments, encoding and character set
$options = array('comment' => 'records meta messages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_from' => array(
		'type' => 'text',
		'length' => 32,
		),

	'user_to' => array(
		'type' => 'text',
		'length' => 32,
		),

	'message' => array(
		'type' => 'text',
		'length' => 255,
		),
         'message_time'=>array(
              'type'=>timestamp)

	);
?>
