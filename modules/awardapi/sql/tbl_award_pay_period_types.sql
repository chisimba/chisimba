<?php
$tablename = "tbl_award_pay_period_types";

$options = array('comment' => 'table to store a list of pay period types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
       'length' => 16
	   ),
	'abbreviation' => array(
	   'type' => 'text',
	   'length' => 3
	   ),
  	'factor' => array(
	   'type' => 'float'
	   )
	);
  
$name = 'tbl_award_pay_period_types_idx';

?>