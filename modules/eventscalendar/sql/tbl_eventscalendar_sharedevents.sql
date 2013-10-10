<?php

$tablename = 'tbl_eventscalendar_sharedevents';

$options = array('comment' => 'Events Calendar Shared Events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'eventid' => array(
		'type' => 'text',
		'length' => 32
		),
	'catid' => array(
		'type' => 'text',
		'length' => 32,
        	'notnull' => TRUE
		),   
    	'sharedwithid' => array(
		'type' => 'text',
		'length' => 32,
	        'notnull' => TRUE
		)   
     
    );

$indexes = array(
                'fields' => array(
                	'eventid' => array()
                )
        );
?>
