<?php

//Table Name
$tablename = 'tbl_switchboard_moduleblocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table holds data pertaining to the blocks displayed on the switchboard module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'block' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE,
        ),
    'side' => array(
        'type' => 'text',
        'length' => 6,
        'nonull' => TRUE,
        ),
    'position' => array(
        'type' => 'integer',
        'length' => 3,
        ),
    'module' => array(
        'type' => 'text',
        'length' => 50
        ), 
    'datelastupdated' => array(
        'type' => 'timestamp'
        ),
    );
    
$name = 'tbl_switchboard_userblocks_idx';

$indexes = array(
    'fields' => array(
        'userid' => array(),
        'block' => array(),
        'side' => array(),
        'position' => array(),
        'module' => array(),
    )
);
?>