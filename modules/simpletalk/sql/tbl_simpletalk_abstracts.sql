<?php
/**
*
* A sample SQL file for simpletalk. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_simpletalk_abstracts';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of abstract data for the simpletalk module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'datecreated' => array(
        'type' => 'timestamp'
    ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
    ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
    ),
    'datemodified' => array(
        'type' => 'timestamp',
    ),
    'emailadr' => array(
        'type' => 'text',
        'length' => 100,
    ),
    'title' => array(
        'type' => 'text',
        'length' => 250,
    ),
    'authors' => array(
        'type' => 'text',
        'length' => 250,
    ),
    'duration' => array(
        'type' => 'text',
        'length' => 10,
        'notnull' => TRUE,
    ),
    'track' => array(
        'type' => 'text',
        'length' => 10,
        'notnull' => TRUE,
    ),
    'abstract' => array(
        'type' => 'clob',
    ),
    'requirements' => array(
        'type' => 'clob',
    ),
);

//create other indexes here...

$name = 'tbl_simpletalk_abstracts_idx';

$indexes = array(
    'fields' => array(
         'title' => array(),
    )
);
?>