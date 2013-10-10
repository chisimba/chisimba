<?php
$tablename = "tbl_award_agree_types";

$options = array('comment' => 'table to store the agreement types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
	   'length' => 64
	   ),
    'abbreviation' => array(
	   'type' => 'text',
	   'length' => 5
	   )
	
	);
	
$name = 'tbl_award_agree_types_idx';

?>