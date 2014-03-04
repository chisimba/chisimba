<?php
$tablename = "tbl_award_benefit_names";

$options = array('comment' => 'table to store a list of benefit names', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'aggregatetype' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE
		),
	'name' => array(
        'type' => 'text',
        'length' => 100,
        'notnull' => TRUE
        ),
    'measure' => array(
		'type' => 'text',
		'length' => 64
		),
    'benchmark' =>array(
        'type' => 'clob'
        )
	
	);
  
$name = 'tbl_award_benefit_names_idx';

?>