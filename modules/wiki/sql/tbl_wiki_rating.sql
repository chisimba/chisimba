<?php
// Table Name
$tablename = 'tbl_wiki_rating';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the wiki page rating.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'page_rating' => array(
		'type' => 'integer',
		'length' => 2,
	),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32,
	),
);
?>