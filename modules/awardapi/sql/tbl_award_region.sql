<?php
$tablename = "tbl_award_region";

$options = array('comment' => 'table to store the regions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'abbreviation' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'name' => array(
	   'type' => 'text',
	   'length' => 255
	   )
	);
	
$name = 'tbl_award_region_idx';

$indexes = array(
                'fields' => array(
                	'id' => array()
                )
        );
?>