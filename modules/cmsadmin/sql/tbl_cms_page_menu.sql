<?php
//5ive definition
$tablename = 'tbl_cms_page_menu';

//Options line for comments, encoding and character set
$options = array('comment' => 'cms page menu', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 32
		),
    'body' => array(
		'type' => 'clob'
		),
    'menukey' => array(
		'type' => 'text',
		'length' => 255
		),
    'userid' => array(
		'type' => 'text',
		'length' => 255
		)

);

?>
