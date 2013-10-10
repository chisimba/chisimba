<?php


$tablename = 'tbl_readinglist_links';

/*
Options line for comments, encoding and character set
*/
$options = array('comment' => '', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

/*Fields
*/
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'itemid' => array(
		'type' => 'text',
		'length' => 32
		),	
	'link' => array(
		'type' => 'text',
		'length' => 255
		)

	);
	


?>