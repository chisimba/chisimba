<?php
// name of the table
$tablename = 'tbl_webpresent_schedules';

// Options line for comments, encoding and character set
$options = array('comment' => 'Store details live presentation schedules', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        ),
    'fileid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        ),
    'schedule_date' => array(
        'type' => 'timestamp',
        'notnull' => TRUE,
        ),
    'status' => array(
        'type' => 'text',
        'length' => 12,
        'notnull' => TRUE,
        ),
    );

// create other indexes here

$name = 'webpresent_schedules_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        'schedule_date' => array(),
        ),
    );
?>
