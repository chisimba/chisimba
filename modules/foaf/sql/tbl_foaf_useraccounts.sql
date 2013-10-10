<?php
// Table Name
$tablename = 'tbl_foaf_useraccounts';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store user FOAF accounts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'accountname' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'accountservicehomepage' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'type' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255,
        )    
    
    );

    //create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                    'userid' => array(),
                )
        );
?>
