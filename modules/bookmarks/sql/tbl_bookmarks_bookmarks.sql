<?php
/**
*
* A sample SQL file for bookmarks. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_bookmarks_bookmarks';

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
	'folder_id' => array(
		'type' => 'text',
                'length' => 32,
		),
	'contextcode' => array(
		'type' => 'text',
                'length' => 255,
                ),
	'bookmark_name' => array(
		'type' => 'text',
		'length' => 250,
		),
	'location' => array(
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

$name = 'tbl_bookmarks_bookmarks_idx';

$indexes = array(
    'fields' => array(
         'id' => array(),
         'user_id' => array(),
         'folder_id' => array(),
         'bookmark_name' => array(),
         'contextcode' => array(),
    )
);
?>