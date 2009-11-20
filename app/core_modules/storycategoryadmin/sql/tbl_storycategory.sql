<?php
// Table Name
$tablename = 'tbl_storycategory';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold story categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'category' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'title' => array(
        'type' => 'text',
        'length' => 250
        ),
    'datecreated' => array(
        'type' => 'date'
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25
        ),
    'datemodified' => array(
        'type' => 'date'
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'modified' => array(
        'type' => 'timestamp'
        )
    );
?>