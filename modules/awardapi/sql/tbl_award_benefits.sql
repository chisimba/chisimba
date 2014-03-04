<?php
$tablename = "tbl_award_benefits";

$options = array('comment' => 'table to store a list of benefits', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'agreeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'nameid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'value' => array(
	   'type' => 'text',
       'length' => 10,
	   ),
    'notes' => array(
		'type' => 'clob'
		)
	
	);
  
$name = 'tbl_award_benefits_idx';

$indexes = array(
                'fields' => array(
                	'agreeid' => array(),
                	'nameid' => array()
                )
        );

?>