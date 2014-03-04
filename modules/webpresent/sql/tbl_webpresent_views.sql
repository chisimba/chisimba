<?php
// name of the table
$tablename = 'tbl_webpresent_views';

// Options line for comments, encoding and character set
$options = array('comment' => 'Track list and number of times a file has been viewed', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'dateviewed' => array(
        'type' => 'date',
        'notnull' => TRUE,
        ),
    'datetimeviewed' => array(
        'type' => 'timestamp',
        'notnull' => TRUE,
        ),
    );

// create other indexes here

$name = 'webpresent_views_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        'dateviewed' => array(),
        ),
    );
?>
