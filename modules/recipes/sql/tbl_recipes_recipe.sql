<?php
// Table Name
$tablename = 'tbl_recipes_recipe';

//Options line for comments, encoding and character set
$options = array('comment' => 'User recipes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'fullname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'yield' => array(
	    'type' => 'text',
	    'length' => 25,
	    ),
	'instructions' => array(
	    'type' => 'clob',
	    ),
	'duration' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'photo' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'summary' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'author' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'published' => array(
	    'type' => 'timestamp',
	    ),
	'nutrition' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'category' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'winepairing' => array(
	    'type' => 'text',
	    'length' => 255,
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
