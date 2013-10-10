<?php
// Table Name
$tablename = 'tbl_foaf_accounts';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store FOAF account types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
        
    'type' => array(
        'type' => 'text',
        'length' => 255,
        )    
    );

    //create other indexes here...

$name = 'id';

$indexes = array(
                'fields' => array(
                    'type' => array(),
                )
        );
?>
