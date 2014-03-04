<?php
// Table Name
$tablename = 'tbl_news_stories';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the news stories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'storytitle' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
    'storytext' => array (
		'type' => 'clob',
		'notnull' => 1
	),
    'storysource' => array (
		'type' => 'text',
	),
    'storylocation' => array (
		'type' => 'text',
		'length' => 32,
	),
	'storycategory' => array (
		'type' => 'text',
		'length' => 32,
	),
    'storydate' => array (
		'type' => 'date',
		'notnull' => 1
	),
    'storyimage' => array (
		'type' => 'text',
		'length' => 32,
	),
	'dateavailable' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
	'datetopstoryexpire' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
	'allowcomments' => array (
		'type' => 'text',
		'length' => 1,
		'default' => 'Y',
	),
    'sticky' => array (
		'type' => 'text',
		'length' => 1,
		'default' => 'Y',
	),
	'storyorder' => array (
		'type' => 'integer',
		'length' => 11,
	),
    'datecreated' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
    'creatorid' => array (
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
	),
    'datemodified' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
    'modifierid' => array (
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
	),

);
//create other indexes here...
$name = 'tbl_news_stories_idx';

$indexes = array(
                'fields' => array(
                	'storytitle' => array(),
                	'storydate' => array(),
                	'storylocation' => array(),
                	'storyimage' => array(),
                	'storycategory' => array(),
                	'dateavailable' => array(),
                	'datetopstoryexpire' => array(),
                	'sticky' => array(),
                	'allowcomments' => array(),
                	'storyorder' => array(),
                	'datecreated' => array(),
                	'datemodified' => array(),
                )
        );
?>