<?php


$tablename = "tbl_award_gender_bcea";
$options = array('comment' => 'Table to store BCEA data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'category' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'type' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'nameid' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'bcea' => array(
	   'type' => 'text',
	   'length' => 32
	   ),
	'comment' => array(
	   'type' => 'text',
	   'length' => 255
	   )
	
	
	);
	
$name = 'tbl_award_gender_bcea_idx';

$indexes = array(
                'fields' => array(
                	 'nameid' => array(),
                          'category' => array()
		)

	);
?>