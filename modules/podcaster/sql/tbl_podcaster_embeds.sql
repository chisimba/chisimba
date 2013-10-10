<?php
// name of the table
$tablename = 'tbl_podcaster_embeds';

// Options line for comments, encoding and character set
$options = array('comment' => 'Track list and number podcasts that are embedded on other sites', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'url' => array (
        'type'=>'text',
        'length'=>255,
        ),
    'dateembedded' => array(
        'type' => 'date',
        'notnull' => TRUE,
        ),
    'datetimeembedded' => array(
        'type' => 'timestamp',
        'notnull' => TRUE,
        ),
    );

// create other indexes here

$name = 'podcaster_embeds_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        'dateembedded' => array()
        )
    );
?>
