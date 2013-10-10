<?php
/**
*
* SQL to generate tbl_registerinterest_interested data
*
*/
// Table Name
$tablename = 'tbl_registerinterest_interested';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of data for the registerinterest module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'fullname' => array(
		'type' => 'text',
		'length' => 250
		),
	'email' => array(
		'type' => 'text',
		'length' => 250
		),
	);

//create other indexes here...

$name = 'tbl_registerinterest_interested_idx';

$indexes = array(
    'fields' => array(
         'fullname' => array(),
         'email' => array()
    )
);
?>