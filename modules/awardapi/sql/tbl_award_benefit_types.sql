<?php
$tablename = "tbl_award_benefit_types";

$options = array('comment' => 'table to store a list of benefit types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
       'length' => 100,
       'notnull' => TRUE
	   )
	);
  
$name = 'tbl_award_benefit_types_idx';

?>