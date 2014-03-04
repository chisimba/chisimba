<?php
// Table Name
$tablename = 'tbl_recipes_cookbooks';

//Options line for comments, encoding and character set
$options = array('comment' => 'User Cookbooks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'cookbookname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'cookbookdesc' => array(
	    'type' => 'clob',
	    ),
	'datecreated' => array(
	    'type' => 'timestamp',
	    ),
	'license' => array(
	    'type' => 'text',
	    'length' => 50,
	    ),
	'favourite' => array(
	    'type' => 'integer',
	    'length' => 1,
	    'default' => 0,
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
