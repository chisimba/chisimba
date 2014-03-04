<?php
// Table Name
$tablename = 'tbl_twitterizer';

//Options line for comments, encoding and character set
$options = array('comment' => 'tweets', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'tweet' => array(
        'type' => 'text',
        'length' => 140
    ),
    'createdat' => array(
        'type' => 'text',
        'length' => 35,
    ),
    'tstamp' => array(
        'type' => 'integer',       
    ),
    'screen_name' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'name' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'image' => array(
        'type' => 'text',
        'length' => 150,
    ),
    'location' => array(
        'type' => 'text',
        'length' => 255,
    ),
);
?>