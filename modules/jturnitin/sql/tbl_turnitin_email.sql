<?php
// Table Name
$tablename = 'tbl_turnitin_email';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold instructor email which is associated with the courses being created in Turnitin', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 32,
        ),       
    'email' => array(
        'type' => 'text',
        'length' => 512,
        ),
    );

?>
