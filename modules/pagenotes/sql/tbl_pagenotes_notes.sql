<?php
/**
*
* A sample SQL file for pagenotes. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_pagenotes_notes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of page level notes within the pagenotes module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'isshared' => array(
	   'type' => 'boolean',
	   'default' => 'false'
    ),
    'note' => array(
        'type' => 'clob',
    ),
);

?>