<?php

$tablename = 'tbl_eventscalendarcategories';

$options = array('comment' => 'Events Calendar Categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'eventid' => array(
		'type' => 'text',
		'length' => 32
		),
	'type' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),   
    'typeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		)
    
    );

$name = 'eventscat';

$indexes = array(
                'fields' => array(
                	'eventid' => array()
                )
        );
?>
