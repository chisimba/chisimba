<?php
// Table Name
$tablename = 'tbl_sahriscollections_collections';

//Options line for comments, encoding and character set
$options = array('comment' => 'SAHRIS collections', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'sitename' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'siteid' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'comment' => array(
	    'type' => 'clob',
	    ),
	'datecreated' => array(
	    'type' => 'timestamp',
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
