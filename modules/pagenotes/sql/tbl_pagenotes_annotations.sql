<?php
/**
*
* A SQL file for pagenotes annotations.
*
*/
// Table Name
$tablename = 'tbl_pagenotes_annotations';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of page level annotations within the pagenotes module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'datecreated' => array(
        'type' => 'timestamp'
        ),
    'datemodified' => array(
        'type' => 'timestamp'
        ),
    'hash' => array(
        'type' => 'text',
        'length' => 250,
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'annotation' => array(
        'type' => 'clob',
        ),
    );

?>