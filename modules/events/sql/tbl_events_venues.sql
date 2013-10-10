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
    'venueaddress' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'city' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'zip' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'phone' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'url' => array(
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
     'privatevenue' => array(
        'type' => 'text',
        'length' => 2,
        ),
    );

//create other indexes here...

$name = 'venuename';

$indexes = array(
                'fields' => array(
                    'venuename' => array('order' => 'desc'),
                )
        );
?>