<?php

/**
*
* Table for holdig history of greetings of users for hellochisimba
*
*/


/*
Set the table name
*/
$tablename = 'tbl_elsitraining_registration';


/*
Options line for comments, encoding and character set
*/
$options = array(
    'comment' => 'Table for registration',
    'collate' => 'utf8_general_ci',
    'character_set' => 'utf8');

/*
Create the table fields
*/
$fields = array(
    'id' => array('type' => 'text','length' => 32),
    'starttime' => array('type' => 'timestamp','notnull' => TRUE),
    'endtime' => array('type' => 'timestamp','notnull' => TRUE),
    'venue' => array('type' => 'text','length' => 128,'notnull' => TRUE),
    'contactperson' => array('type' => 'text','length' => 12,'notnull' => TRUE),
    'maxlimit' => array('type' => 'integer','length' => 10,'notnull' => TRUE),
    'description' => array('type' => 'text','length' => 255,'notnull' => TRUE)
);
?>