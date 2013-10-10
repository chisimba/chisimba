<?php
// Table Name
$tablename = 'tbl_das_chat';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold DAS alias', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'body' => array(
        'type' => 'clob',
    	),
    'datesent' => array(
        'type' => 'timestamp',
        ),    
    );

?>
