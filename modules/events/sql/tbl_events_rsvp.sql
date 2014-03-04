<?php
// Table Name
$tablename = 'tbl_events_rsvp';

//Options line for comments, encoding and character set
$options = array('comment' => 'RSVP list for events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'eventid' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'ans' => array(
        'type' => 'text',
        'length' => 10,
        ),
);

//create other indexes here...

$name = 'mediatag';

$indexes = array(
                'fields' => array(
                    'eventid' => array('order' => 'desc'),
                )
        );
?>