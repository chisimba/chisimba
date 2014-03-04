<?php
$tablename = "tbl_award_index_values";

$options = array('comment' => 'table to store a list of index values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'typeid' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'indexdate' => array(
	   'type' => 'date'
	   ),
	'value' => array(
	   'type' => 'float',
	   'length' => 20
	   )
	);
	
$name = 'tbl_award_index_values_idx';

$indexes = array(
                'fields' => array(
                	'typeid' => array(),
                	'indexdate' => array()
                )
        );
?>