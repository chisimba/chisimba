<?php
/**
*
* Table for holdig history of greetings of users for hellochisimba
*
*/
/*
Set the table name
*/
$tablename = 'tbl_mxit_words';
/*
Options line for comments, encoding and character set
*/
$options = array(
    'comment' => 'Table for tbl_mxit_tbl_mxit_suggested',
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
    'word' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'definition' => array(
        'type' => 'text',
		'length' => 50,
        'notnull' => TRUE
        )
);
?>
