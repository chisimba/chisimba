<?php
// Table Name
$tablename = 'tbl_wiki_forum';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the wiki page discussion data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'post_title' => array(
		'type' => 'text',
		'length' => 255,
	),
	'post_content' => array(
		'type' => 'clob',
	),
	'post_order' => array(
		'type' => 'integer',
		'length' => 4,
	),
	'post_status' => array( // 1 => active, 2 => deleted
		'type' => 'integer',
		'length' => 1,
	),
	'author_id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'date_created' => array(
		'type' => 'timestamp',
	),
	'date_modified' => array(
		'type' => 'timestamp',
	),
);
?>