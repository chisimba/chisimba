<?php
$tablename = "tbl_award_sicmajordiv";

$options = array('comment' => 'table to store a list of sic major divisions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
	   'type' => 'text',
	   'length' => 92
	   ),
	'fullname' => array(
	   'type' => 'text',
	   'length' => 200
	   ),
	'code' => array(
	   'type' => 'integer',
	   'length' => 2
	   ),
	'notes' => array(
	   'type' => 'clob'
	   )
	);
	
$name = 'tbl_award_sicmajordiv_idx';

?>