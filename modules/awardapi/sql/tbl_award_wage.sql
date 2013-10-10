<?php
$tablename = "tbl_award_wage";

$options = array('comment' => 'table to store a list of wage values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'payperiodtypeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'weeklyrate' => array(
	   'type' => 'float'
	   ),
	'notes' => array(
	   'type' => 'text',
	   'length' => 150
	   )
	);
  
$name = 'tbl_award_wage_idx';

$indexes = array(
                'fields' => array(
                	'agreeid' => array(),
                    'payperiodtypeid' => array()
                )
        );
?>