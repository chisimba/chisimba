<?php
// Table Name
$tablename = 'tbl_events_venue_location';

//Options line for comments, encoding and character set
$options = array('comment' => 'venue locations', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'venueid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'fcodename' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'countryname' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'countrycode' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'fcl' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'fclname' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'lng' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'fcode' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'geonameid' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'lat' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'admincode1' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'adminname1' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'population' => array(
        'type' => 'text',
        'length' => 255,
        ),
    );

//create other indexes here...

$name = 'venueid';

$indexes = array(
                'fields' => array(
                    'venueid' => array('order' => 'desc'),
                )
        );
?>