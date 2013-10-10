<?php
// name of the table
$tablename = 'tbl_webpresent_tagviews';

// Options line for comments, encoding and character set
$options = array('comment' => 'Track list and number of times a tag has been viewed', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        ),
    'tag' => array(
        'type' => 'text',
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

?>