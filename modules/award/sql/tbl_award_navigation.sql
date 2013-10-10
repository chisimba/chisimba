<?php

$tablename = "tbl_award_navigation";
$options = array('comment' => 'table to store navigation data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        	'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
	   'length' => 64
	   ),
	'action' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'accesslevel' => array(
	   'type' => 'integer',
	   'length' => 2
	   )
	);
	
$name = 'tbl_award_navigation_idx';

$indexes = array(
                'fields' => array(
                	 'name' => array()
		)

	);


?>
