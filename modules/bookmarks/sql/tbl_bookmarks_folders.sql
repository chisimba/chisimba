<?php
/**
*
* A sample SQL file for bookmarks. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_bookmarks_folders';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of text for the bookmarks module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'user_id' => array(
		'type' => 'text',
                'length' => 32,
		),
	'parent_id' => array(
		'type' => 'text',
                'length' => 32,
		),
	'folder_name' => array(
		'type' => 'text',
		'length' => 250,
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'modified_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_modified' => array(
		'type' => 'timestamp'
		),

	);

//create other indexes here...

$name = 'tbl_bookmarks_folders_idx';

$indexes = array(
    'fields' => array(
         'id' => array(),
         'user_id' => array(),
         'parent_id' => array(),
    )
);
?>