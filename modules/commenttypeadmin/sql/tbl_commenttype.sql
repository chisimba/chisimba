<?php
//Table Name
$tablename = 'tbl_commenttype';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the commenttype', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),

	'type' => array(
		'type'=>'text',
		'length'=> 32,
		),
	'title' => array(
		'type'=>'text',
		'length'=> 250,
		),
	'datecreated' => array(
		'type' => 'date'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'datemodified' => array(
		'type'=> 'date'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'modified' => array(
		'type' => 'text',
		'length' => 14,
		'notnull' => 1
		)

         );
?>
