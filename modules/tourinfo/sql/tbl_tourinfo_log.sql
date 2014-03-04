<?php
// Table Name
$tablename = 'tbl_tourinfo_log';

//Options line for comments, encoding and character set
$options = array('comment' => 'tourism info data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'msgtype' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'msgfrom' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'msgbody' => array(
        'type' => 'clob',
    ),
    'datesent' => array(
        'type' => 'timestamp',
    ),
    'shortcode' => array(
        'type' => 'text',
        'length' => 100,
        'default' => '0',
    ),
    'geolat' => array(
        'type' => 'text',
        'length' => 100,
        'default' => '0',
    ),
    'geolon' => array(
        'type' => 'text',
        'length' => 100,
        'default' => '0',
    ),
);
?>