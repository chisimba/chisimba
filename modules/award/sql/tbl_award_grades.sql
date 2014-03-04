<?php


$tablename = "tbl_award_grades";

$options = array('comment' => 'Table to store award grades.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'name' => array(
		'type' => 'text',
		'length' => 200,
	   )
	);
	
$name = 'tbl_award_grades_idx';

$indexes = array(
                'fields' => array(
                    'id' => array()

                )
        );
?>
