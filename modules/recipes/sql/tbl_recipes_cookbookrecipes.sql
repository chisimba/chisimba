<?php
// Table Name
$tablename = 'tbl_recipes_cookbookrecipes';

//Options line for comments, encoding and character set
$options = array('comment' => 'User Cookbook recipes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'cookbookid' => array(
	    'type' => 'text',
	    'length' => 50,
	    ),
	'recipeid' => array(
	    'type' => 'text',
	    'length' => 50,
	    ),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>
