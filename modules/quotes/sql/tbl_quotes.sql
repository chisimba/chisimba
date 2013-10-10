<?php
//Table Name
$tablename = 'tbl_quotes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the random quotes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'quote' => array(
		'type'=>'clob'
		),
	'whosaidit' => array(
		'type'=>'text',
		'length'=> 150
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datemodified' => array(
		'type'=> 'timestamp'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32 	
		),
	'modified' => array(
		'type' => 'timestamp',
		//'length' => 14,
		'notnull' => TRUE
		)
	);
?>
