<?php
/**
*Table structure for table `tbl_glossary_images`
*
*@author Alastair Pursch
*
*@package glossary
* 
*/

$tablename = 'tbl_glossary_images';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_glossary_images', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'item_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'image' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),		
	'caption' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => 1
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1		
		),	
	'datelastupdated' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	);
?>