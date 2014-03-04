<?php
// Table Name
$tablename = 'tbl_events_promoters';

//Options line for comments, encoding and character set
$options = array('comment' => 'event self promo events', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'event_id' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'canbringothers' => array(
        'type' => 'text',
        'length' => 10,
        ),
    'numberguests' => array(
        'type' => 'text',
        'length' => 10,
        ),
     'limitedto' => array(
        'type' => 'text',
        'length' => 50,
        ),
    );
?>