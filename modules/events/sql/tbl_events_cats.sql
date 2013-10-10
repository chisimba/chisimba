<?php
// Table Name
$tablename = 'tbl_events_cats';

//Options line for comments, encoding and character set
$options = array('comment' => 'event categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'cat_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'cat_desc' => array(
        'type' => 'clob',
        ),
    );

//create other indexes here...

$name = 'cat_name';

$indexes = array(
                'fields' => array(
                    'cat_name' => array('order' => 'desc'),
                )
        );
?>