<?php

/*

Todo: Investigate possibility of tree menu

*/


// Table Name
$tablename = 'tbl_news_menu';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the news menu structure', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'itemtype' => array (
		'type' => 'text',
		'length' =>32,
		'notnull' => 1
	),
	'itemvalue' => array (
		'type' => 'text',
		'length' =>255,
		'notnull' => 1
	),
	'itemname' => array (
		'type' => 'text',
		'length' =>50,
		'notnull' => 1
	),
	'itemorder' => array (
		'type' => 'integer',
		'length' =>11,
	),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_menu_idx';

$indexes = array(
                'fields' => array(
                	'itemorder' => array(),
                	'itemtype' => array(),
                	'itemname' => array(),
                )
        );
		



?>