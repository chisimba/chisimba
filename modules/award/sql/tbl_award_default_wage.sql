<?php

$tablename = "tbl_award_default_wage";
$options = array('comment' => 'Table to store default wage aggregation data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'year' => array(
	   'type' => 'integer',
	   'length' => 4
	   ),
	'sample' => array(
	   'type' => 'integer',
	   'length' => 5
	   ),
	'value' => array(
	   'type' => 'text',
	   'length' => 16
	   ),
	'inc' => array(
	   'type' => 'text',
	   'length' => 16
	   ),
	'actual' => array(
	   'type' => 'text',
	   'length' => 16
	   ),
	'workers' => array(
	   'type' => 'integer',
            'lenght' => 6
	   )	
	);
	
$name = 'tbl_award_default_wage_idx';

$indexes = array(
                'fields' => array(
                	  'value' => array(),
                          'sample' => array()
		)

	);

?>