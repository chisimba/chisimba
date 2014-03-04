<?php
// Table Name
$tablename = 'tbl_events_events';

//Options line for comments, encoding and character set
$options = array('comment' => 'events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'venue_id' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'category_id' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'start_date' => array(
        'type' => 'date',
        ),
    'end_date' => array(
        'type' => 'date',
        ),
    'start_time' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'end_time' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'description' => array(
        'type' => 'clob',
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'personal' => array(
        'type' => 'text',
        'length' => 2,
        ),
    'selfpromotion' => array(
        'type' => 'text',
        'length' => 2,
        ),
    'ticket_url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'ticket_price' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'ticket_free' => array(
        'type' => 'text',
        'length' => 2,
        ),
    'creationtime' => array(
        'type' => 'text',
        'length' =>50,
        ),
    'twitoasterid' => array (
        'type' => 'text',
        'length' => 50,
        ),
    );
//create other indexes here...

?>