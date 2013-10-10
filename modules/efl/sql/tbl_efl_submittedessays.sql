<?php

$tablename = 'tbl_efl_submittedessays';



$options = array(
    'comment' => 'Table for tbl_efl_submittedessays',
    'collate' => 'utf8_general_ci', 
    'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'essayid' => array(
        'type' => 'text',
        'length' => 150,
        'notnull' => 1
        ),

    'content' => array(
        'type' => 'text'
        ), 
    'submitdate' => array(
        'type' => 'timestamp',
        'length' => 15,
        'notnull' => TRUE
        )
);
?>
