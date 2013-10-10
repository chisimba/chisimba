<?php
/**
*
*Table for holding comments submitted by user
*
*/

/*
Set the table name
*/
$tablename = 'tbl_hosportal_messages';

/*
Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table for hosportal_messages',
 'character_set' => 'utfs');


/*
Create the table fields
*/
$fields = array(
 'id' => array(
 'type' => 'text',
 'length' => 32,
 'notnull' => 1
),
 'userid' => array(
 'type' => 'text',
 'length' => 35,
 'notnull' => TRUE
),
 'title' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'commenttxt' => array(
 'type' => 'clob',
),
 'modified' => array(
 'type' => 'timestamp',
 'notnull' => TRUE
),
 'commenttxtshort' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
'unreplied' => array(
 'type' => 'boolean',
 'notnull'
),
'replies' => array(
 'type' => 'integer',
 'notnull'
)
);
?>
