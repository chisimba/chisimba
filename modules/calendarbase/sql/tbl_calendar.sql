<?php

//5ive definition
$tablename = 'tbl_calendar';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold calendar events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'multiday_event' => array(
        'type' => 'text',
        'length' => 10,
        'notnull' => 1,
        'default' => 0
        ),
    'eventdate' => array(
        'type' => 'date',
        ),
    'multiday_event_start_id' => array(
        'type' => 'text'
        ),
    'eventtitle' => array(
        'type' => 'text',
        'length' => 100
        ),
    'eventdetails' => array(
        'type' => 'text',
        ),
    'eventurl' => array(
        'type' => 'text',
        'length' => 100
        ),
    'userorcontext' => array(
        'type' => 'text',
        'length' => 10
        ),
    'context' => array(
        'type' => 'text',
        'length' => 32
        ),
    'workgroup' => array(
        'type' => 'text',
        'length' => 32
        ),
    'showusers' => array(
        'type' => 'text',
        'length' => 10
        ),
    'userFirstEntry' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'userLastModified' => array(
        'type' => 'text',
        'length' => 32
        ),
    'dateFirstEntry' => array(
        'type' => 'date',
        ),
    'dateLastModified' => array(
        'type' => 'date',
        ),
    'updated' => array(
        'type' => 'date',
        ),
    'timefrom' => array(
        'type' => 'time',
        ),
    'timeto' => array(
        'type' => 'time',
        ),
    'moduleevent_table' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'moduleevent_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'alert_state' => array(
        'type' => 'text',
        'length' => 1,
        'default' => 0,
        ),
    );

//create other indexes here...


?>