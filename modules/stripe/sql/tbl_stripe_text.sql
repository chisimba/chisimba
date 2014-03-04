<?php
/**
*
* A sample SQL file for stripe. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_stripe_text';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of text for the stripe module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'title' => array(
		'type' => 'text',
		'length' => 250,
		),
	'content' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'tbl_stripe_text_idx';

$indexes = array(
    'fields' => array(
         'title' => array(),
    )
);
?>