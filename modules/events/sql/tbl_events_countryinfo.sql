<?php
// Table Name
    $tablename = 'tbl_events_countryinfo';

//Options line for comments, encoding and character set
$options = array('comment' => 'Basic country info', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'countryname' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'countrycode' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'isonumeric' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'isoalpha3' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'fipscode' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'continent' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'capital' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'areainsqkm' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'population' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'currencycode' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'languages' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'geonameid' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'bboxwest' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'bboxnorth' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'bboxeast' => array(
        'type' => 'text',
        'length' => 255,
        ),
     'bboxsouth' => array(
        'type' => 'text',
        'length' => 255,
        ),
    );

//create other indexes here...

$name = 'countryname';

$indexes = array(
                'fields' => array(
                    'countryname' => array('order' => 'desc'),
                )
        );
?>