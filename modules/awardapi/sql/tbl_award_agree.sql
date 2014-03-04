<?php
$tablename = "tbl_award_agree";

$options = array('comment' => 'table to store the agreements', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'typeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'unitid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
	   'length' => 255
	   ),
	'implementation' => array(
	   'type' => 'date'
	   ),
	'length' => array(
	   'type' => 'integer',
	   'length' => 2
	   ),
	'workers' => array(
	   'type' => 'integer',
	   'length' => 4
	   ),
	'notes' => array(
		'type' => 'clob'
        ),
	'active' => array(
	   'type' => 'integer',
	   'length' => 1
	   )
	);
	
$name = 'tbl_award_agree_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'typeid' => array(),
                	'unitid' => array()
                )
        );
?>