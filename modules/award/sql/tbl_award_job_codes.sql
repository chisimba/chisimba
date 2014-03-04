<?php

$tablename = "tbl_award_job_codes";

$options = array('comment' => 'Table to store award job codes.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
		'type' => 'text',
		'length' => 2,
	   )
        'description' => array(
		'type' => 'text',
		'length' => 200,
	   )
        'notes' => array(
		'type' => 'text',
		'length' => 200,
	   )
	);
	
$name = 'tbl_award_job_codes_idx';

$indexes = array(
                'fields' => array(
                    'id' => array()

                )
        );

?>
