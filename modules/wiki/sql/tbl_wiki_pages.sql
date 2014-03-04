<?php
// Table Name
$tablename = 'tbl_wiki_pages';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the wiki data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'wiki_id' => array(
	   'type' => 'text',
	   'length' => 32,
    ),
    'page_name' => array(
        'type' => 'text',
        'length' => 255,
    ),
	'main_page' => array(
		'type' => 'integer',
		'length' => 1,
	),
	'page_summary' => array(
		'type' => 'clob',
	),
	'page_content' => array(
		'type' => 'clob',
	),
	'page_version' => array(
		'type' => 'integer',
		'length' => 4,
	),
	'version_comment' => array(
		'type' => 'clob',
	),
	'page_status' => array( // current, restored, reinstated, overwritten, archived, deleted
		'type' => 'integer',
		'length' => 1,
	),
	'page_author_id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'date_created' => array(
		'type' => 'timestamp',
	),
);
?>