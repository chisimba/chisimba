<?php
$tablename = "tbl_award_socmajorgroup";

$options = array('comment' => 'table to store a list of soc major groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
	   'type' => 'text',
	   'length' => 64
	   )
	);
	
$name = 'tbl_award_socmajorgroup_idx';

?>