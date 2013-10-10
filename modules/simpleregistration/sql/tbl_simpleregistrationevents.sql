<?php
// Table Name
$tablename = 'tbl_simpleregistrationevents';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table holding the event listing', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
    'event_title' => array(
    'type' => 'text',
    'length' => 128
    ),
    'short_name' => array(
    'type' => 'text',
    'length' => 128
    ),
    'max_people' => array(
    'type' => 'integer',
    'notnull' => TRUE
    ),
    'event_date' => array(
    'type' => 'date'
    ),
    'userid' => array(
    'type' => 'text',
    'length' => 255
    )
    );
?>
