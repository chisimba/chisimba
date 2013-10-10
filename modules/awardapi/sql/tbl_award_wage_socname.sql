<?php
$tablename = "tbl_award_wage_socname";

$options = array('comment' => 'table to store a relation of wages to soc names', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'socnameid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'gradeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'jobcodeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		)
	);
	
$name = 'tbl_award_wage_socname_idx';

$indexes = array(
                'fields' => array(
                	'socnameid' => array(),
                    'gradeid' => array(),
                    'jobcodeid' => array()
                )
        );

?>