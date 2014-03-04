<?php
/**
*
* A sample SQL file for statusbar. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_statusbar_bridging';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of bridging for the statusbar module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'activity_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'calendar_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'photo_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'alert_state' => array(
        'type' => 'text',
        'length' => 1,
        'default' => 0,
    ),
);

//create other indexes here...

$name = 'tbl_statusbar_settings_idx';

$indexes = array(
    'fields' => array(
         'id' => array(),
         'user_id' => array(),
    )
);
?>