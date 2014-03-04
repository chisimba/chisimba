<?php
// Table Name
$tablename = 'tbl_das_usersarchive';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold IM users and contacts associations', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'person' => array(
        'type' => 'text',
        'length' => 255,
        ), 
    'patients' => array(
        'type' => 'integer',        
        ),    
    'dateoflastcontact' => array(
        'type' => 'timestamp',
        ),
    );

?>
