<?php

/**
*
* Table for holdig history of greetings of users for hellochisimba
*
*/


/*
Set the table name
*/
$tablename = 'tbl_elsitraining_scheduledregistration';


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
    'staffnum' => array('type' => 'text','length' => 12,'notnull' => TRUE),
    'title' => array('type' => 'text','length' => 8,'notnull' => TRUE),
    'firstname' => array('type' => 'text','length' => 25,'notnull' => TRUE),
    'surname' => array('type' => 'text','length' => 25,'notnull' => TRUE),
    'email' => array('type' => 'text','length' => 30,'notnull' => TRUE),
    'tel' => array('type' => 'text','length' => 5,'notnull' => TRUE),
    'TID' => array('type' => 'text','length' => 32,'notnull' => TRUE),
    'refnum' => array('type' => 'text','length' => 12,'notnull' => TRUE),
    'ovrbook' => array('type' => 'text','length' => 1,'notnull' => TRUE),
    'canceled' => array('type' => 'text','length' => 1,'notnull' => TRUE)
);
?>