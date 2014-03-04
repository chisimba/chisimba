<?php
$tablename = "tbl_award_party";

$options = array('comment' => 'table to store a list of party values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
	   'type' => 'text',
       'length' => 255
	   ),
	'abbreviation' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
  	'registrationnumber' => array(
	   'type' => 'text',
	   'length' => 50
	   )
	);
  
$name = 'tbl_award_party_idx';

$indexes = array(
                'fields' => array(
                	'id' => array()
                )
        );
?>