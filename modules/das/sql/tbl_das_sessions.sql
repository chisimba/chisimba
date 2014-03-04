<?php
// Table Name
$tablename = 'tbl_das_sessions';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold session info for das', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),  
    'datesent' => array(
        'type' => 'timestamp',
        ),
    );

?>