<?php
/**
* Database Table Activity
* @author Wesley Nitsckie
* @copyright 2008 University of the Western Cape
*/

//Chisimba definition
$tablename = 'tbl_activity';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to store activity list', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'module' => array(
		'type' => 'text',
		'length' => 32,
	),
	'contextcode' => array(
		'type' => 'text',
		'length' => 32,
	),
	'title' => array(
		'type' => 'text',
		'length' => 64,
	),
	'description' => array(
		'type' => 'text'
	),
	'createdon' => array(
		'type' => 'timestamp',
	),
	'createdby' => array(
		'type' => 'integer',
		'length' => 10,
	),
	'link' => array(
		'type' => 'text',		
	)
	
);
// Other indicies
$name = 'contextcodex';
$indexes = array(
    'fields' => array(
        'contextcode' => array()
    )
);
?>