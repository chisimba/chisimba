<?php
$tablename = "tbl_award_unit";

$options = array('comment' => 'table to store the bargaining units', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
	   'length' => 255
	   ),
	'oldname' => array(
	   'type' => 'text',
	   'length' => 25
	   ),
	'notes' => array(
	   'type' => 'text',
	   'length' => 255
	   ),
	'active' => array(
	   'type' => 'integer',
	   'length' => 1
	   )
	);
	
$name = 'tbl_award_units_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'name' => array()
                )
        );
?>