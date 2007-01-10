<?php
// Table Name
$tablename = 'tbl_languagetext';

//Options line for comments, encoding and character set
$options = array('comment'=>'languagetext','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'code' => array(
		'type' => 'text',
		'length' => 50,

		),
    'description' => array(
		'type' => 'clob',
		)
    );

?>