<?php
// name of the table
$tablename = 'tbl_webpresent_downloads';

// Options line for comments, encoding and character set
$options = array('comment' => 'Track list and number of times a file has been downloaded', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'filetype' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        ),
    'datedownloaded' => array(
        'type' => 'date',
        'notnull' => TRUE,
        ),
    'datetimedownloaded' => array(
        'type' => 'timestamp',
        'notnull' => TRUE,
        ),
    );

// create other indexes here

$name = 'webpresent_downloads_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        'datedownloaded' => array(),
        ),
    );
?>
