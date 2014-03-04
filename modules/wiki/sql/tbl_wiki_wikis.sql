<?php
// Table Name
$tablename = 'tbl_wiki_wikis';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep the wiki data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'group_type' => array( // default, context, personal etc
	   'type' => 'text',
	   'length' => 255,
    ),
    'group_id' => array( // context code, user id etc
        'type' => 'text',
        'length' => 255,
    ),
    'wiki_name' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'wiki_description' => array(
        'type' => 'clob',
    ),
	'wiki_visibility' => array( // 1 = public, 2 = open, 3 = private
		'type' => 'integer',
		'length' => 1,
	),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'date_created' => array(
		'type' => 'timestamp',
	),
);
?>