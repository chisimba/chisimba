<?php
/**
*
* A sample SQL file for ckeditor2. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_ckeditor2_text';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of text for the ckeditor2 module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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

$name = 'tbl_ckeditor2_text_idx';

$indexes = array(
    'fields' => array(
         'title' => array(),
    )
);
?>