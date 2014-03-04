<?php
$tablename = "tbl_award_indexes";

$options = array('comment' => 'table to store a list of indexes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'shortname' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'name' => array(
	   'type' => 'text',
	   'length' => 255
	   ),
	'description' => array(
	   'type' => 'clob'
	   ),
	'period' => array(
	   'type' => 'integer',
	   'default' => '1',
	   'notnull' => TRUE
	   ),
	'display' => array(
	   'type' => 'boolean',
	   'default' => 'true'
	   )
	);
	
$name = 'tbl_award_indexes_idx';

?>