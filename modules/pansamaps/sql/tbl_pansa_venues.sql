<?php
// Table Name
$tablename = 'tbl_pansa_venues';

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
    'venueaddress1' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'venueaddress2' => array(
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
    'phonecode' => array(
        'type' => 'text',
        'length' => 5,
        ),
    'phone' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'faxcode' => array(
        'type' => 'text',
        'length' => 5,
        ),
    'fax' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'email' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'contactperson' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'otherinfo' => array(
        'type' => 'clob',
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
     'venuelocation' => array(
        'type' => 'text',
        'length' => 255,
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
