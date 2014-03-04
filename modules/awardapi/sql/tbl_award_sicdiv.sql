<?php
$tablename = "tbl_award_sicdiv";

$options = array('comment' => 'table to store a list of sic divisions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'major_divid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
	   'type' => 'text',
	   'length' => 200
	   ),
	'code' => array(
	   'type' => 'integer',
	   'length' => 2
	   ),
	'notes' => array(
	   'type' => 'clob'
	   )
	);
	
$name = 'tbl_award_sicdiv_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'major_divid' => array()
                )
        );

?>