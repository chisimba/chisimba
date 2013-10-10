<?php
$tablename = "tbl_award_district";

$options = array('comment' => 'table to store a list of district values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'name' => array(
	   'type' => 'text',
       'length' => 255,
	   ),
	'urbanindicator' => array(
	   'type' => 'text',
	   'length' => 32
	   )
	);
  
$name = 'tbl_award_district_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'regionid' => array()
                )
        );
?>