<?php

/**
 *
 * Table for holdig history of greetings of users for hellochisimba
 *
 */
/*
  Set the table name
 */
$tablename = 'tbl_sasicontext';
/*
  Options line for comments, encoding and character set
 */
$options = array(
    'comment' => 'Table for tbl_sasicontext',
    'collate' => 'utf8_general_ci',
    'character_set' => 'utf8');
/*
  Create the table fields
 */
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1
    ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => 1
    ),
    'faculty' => array(
        'type' => 'text',
        'length' => 3,
        'notnull' => 1
    ),
    'facultytitle' => array(
        'type' => 'text',
        'length' => 250,
        'notnull' => 1
    ),
    'department' => array(
        'type' => 'text',
        'length' => 6,
        'notnull' => 1
    ),
    'departmenttitle' => array(
        'type' => 'text',
        'length' => 250,
        'notnull' => 1
    ),
    'subject' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => 1
    ),
    'subjecttitle' => array(
        'type' => 'text',
        'length' => 250,
        'notnull' => 1
    ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => 1
    ),
    'last_modified' => array(
        'type' => 'timestamp'
    )
);
?>