<?php
// Table Name
$tablename = 'tbl_wiki_watch';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the wiki page watchlist.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'creator_id' => array(
		'type' => 'text',
		'length' => 32,
	),
);
?>