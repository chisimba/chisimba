<?php
// Table Name
$tablename = 'tbl_wiki_links';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the interwiki link data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'wiki_name' => array(
	   'type' => 'text',
	   'length' => 255,
    ),
    'wiki_link' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'creator_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
);
?>