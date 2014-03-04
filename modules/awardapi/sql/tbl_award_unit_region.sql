<?php
$tablename = "tbl_award_unit_region";

$options = array('comment' => 'table to store a list of unit and region id values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'regionid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'unitid' => array(
	   'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
	   )
	);
  
$name = 'tbl_award_unit_region_idx';

$indexes = array(
                'fields' => array(
                	'regionid' => array(),
                	'unitid' => array()
                )
        );
?>