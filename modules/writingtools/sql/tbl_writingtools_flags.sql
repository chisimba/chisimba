<?php

/**
*
* Table for holding the writing flags for the writing tools module
*
*/


/*
Set the table name
*/
$tablename = 'tbl_writingtools_flags';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for flags used in writing tools', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1
        ),
    'handle' => array(
        'type' => 'text',
        'length' => 255
    ),
    'title' => array(
        'type' => 'clob'
        ),
    'description' => array(
        'type' => 'clob'
        ),
    'notes' => array(
        'type' => 'clob'
        ),
    'iconurl' => array(
        'type' => 'text',
        'length' => 255
        ),
    'moreinfourl' => array(
        'type' => 'text',
        'length' => 255
        ),
    'created' => array(
        'type' => 'timestamp',
        'notnull' => 1
        ),
    'modified' => array(
        'type' => 'timestamp'
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 32
        )
);
?>