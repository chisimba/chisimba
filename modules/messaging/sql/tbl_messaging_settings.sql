<?php
//5ive definition
$tablename = 'tbl_messaging_settings';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table to hold message settings', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'delivery_type' => array( // anytime, on logon, time interval
        'type' => 'integer',
        'length' => 1,
        ),
    'time_interval' => array( // time in minutes
        'type' => 'integer',
        'length' => 2,
        ),
    'name_display' => array( // full, username only
        'type' => 'integer',
        'length' => 2,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'tbl_messaging_settings_index';

$indexes = array(
                'fields' => array(
                    'user_id' => array(),
                )
        );
?>