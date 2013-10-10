<?php
// Table Name
$tablename = 'tbl_bmmentions';

//Options line for comments, encoding and character set
$options = array('comment' => 'brand mentions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'from_user' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'to_user' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'tweetid' => array(
        'type' => 'text',
        'length' => 20,
    ),
    'lang' => array(
        'type' => 'text',
        'length' => 2,
    ),
    'source' => array(
        'type' => 'text',
        'length' => 50,
    ),
    'image' => array(
        'type' => 'text',
        'length' => 150,
    ),
    'location' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'tweettime' => array(
        'type' => 'text',
        'length' => 100,
     ),
);
?>