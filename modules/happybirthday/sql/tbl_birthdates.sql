<?php
/**
*
* Table for holdig the birth dates for users
*
*/
/*
Set the table name
*/
$tablename = 'tbl_birthdates';
/*
Options line for comments, encoding and character set
*/
$options = array(
    'comment' => 'Table for tbl_birthdates',
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

    'username' => array(
        'type' => 'text',
        'length' => 30,
        'notnull' => 'TRUE'
        ),
    'firstname' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
        ),
    'birthdate' => array(
        'type' => 'date',
        'notnull' => TRUE
        )
);
?>
