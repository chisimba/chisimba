<?php

//Table Name
$tablename = 'tbl_comment';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the comments ','collate' => 'utf8_general_ci', 'character_set' => 'utf8');
//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1,
		),

	'tablename' => array(
		'type' => 'text',
		'length' => 250,
		),
	'sourceid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'type' => array(
		'type' => 'text',
		'length' => 50,
		),
    'commenttext' => array(
		'type' => 'clob',
		),
	'approved' => array(
		'type' => 'integer',
		'length' => 3,
		),
	'datecreated' => array(
		'type' => 'date'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 25,
		),
   	'datemodified' => array(
		'type'=> 'date'
		),

	'modifierid' => array(
		'type' => 'text',
		'length' => 24,
		),
	'modified' => array(
		'type' => 'text',
		'length' => 14,
		'notnull' => 1
		)
    	);

?>