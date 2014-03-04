<?php
// Table Name
$tablename = 'tbl_simpletalk_durations';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of duration data for the simpletalk module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'duration' => array(
        'type' => 'text',
        'length' => 25,
    ),
    'duration_label' => array(
        'type' => 'text',
        'length' => 250,
    ),
);

//create other indexes here...
$name = 'tbl_simpletalk_durations_idx';
$indexes = array(
    'fields' => array(
         'duration' => array(),
    )
);
?>