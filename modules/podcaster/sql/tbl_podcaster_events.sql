<?php
// name of the table
$tablename = 'tbl_podcaster_events';

// Options line for comments, encoding and character set
$options = array('comment' => 'Helps in grouping events into various categories and specify their access level', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
	'notnull' => TRUE,
        ),
	'eventid' => array(
        'type' => 'text',
        'length' => 32,
	'notnull' => TRUE,
        ),
	'categoryid' => array(
        'type' => 'text',
        'length' => 32,
	'notnull' => TRUE,
        ),
	'access' => array(
        'type' => 'text',
        'length' => 32,
	'notnull' => TRUE,
        ),
	'publish_status' => array(
        'type' => 'text',
        'length' => 32,
	'notnull' => TRUE,
        )
    );
// create other indexes here

$name = 'podcaster_events_index';

$indexes = array(
    'fields' => array(
        'eventid' => array(),
        ),
    );
?>