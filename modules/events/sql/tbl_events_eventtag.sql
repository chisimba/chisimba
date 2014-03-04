<?php
// Table Name
$tablename = 'tbl_events_eventtag';

//Options line for comments, encoding and character set
$options = array('comment' => 'social site tag (generated)', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'eventid' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'mediatag' => array(
        'type' => 'text',
        'length' => 20,
        ),
);

//create other indexes here...

$name = 'mediatag';

$indexes = array(
                'fields' => array(
                    'mediatag' => array('order' => 'desc'),
                )
        );
?>