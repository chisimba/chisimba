<?php
$tablename = 'tbl_eventscalendar';

$options = array('comment' => 'Events Calendar Information Information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	
	'title' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),   
    'description' => array(
		'type' => 'text'
		),
    'catid' => array(
		'type' => 'text',
		'length' => 32
		),
	'location' => array(
		'type' => 'text',
		'length' => 255
		),
    'event_date' => array(
		'type' => 'integer'
		),
    'start_time' => array(
        'type' => 'integer'
        ),
    'end_time' => array(
        'type' => 'integer'
        )

    );

$name = 'events';

$indexes = array(
                'fields' => array(
                	'catid' => array()
                )
        );
?>