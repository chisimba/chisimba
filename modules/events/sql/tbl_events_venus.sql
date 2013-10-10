<?php
// Table Name
$tablename = 'tbl_events_venues';

//Options line for comments, encoding and character set
$options = array('comment' => 'venues', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'venuename' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'venuedescription' => array(
        'type' => 'clob',
        ),
    'geolat' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'geolon' => array(
        'type' => 'text',
        'length' => 255,
        ),
    );

//create other indexes here...

$name = 'cat_name';

$indexes = array(
                'fields' => array(
                    'venuename' => array('order' => 'desc'),
                )
        );
?>