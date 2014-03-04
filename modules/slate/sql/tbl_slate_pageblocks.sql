<?php

//Table Name
$tablename = 'tbl_slate_pageblocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table holds data pertaining to the blocks displayed on the slate module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'pageid' => array(
        'type' => 'text',
        'length' => 32
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
    
$name = 'tbl_slate_pageblocks_idx';

$indexes = array(
    'fields' => array(
        'pageid' => array(),
        'block' => array(),
        'side' => array(),
        'position' => array(),
        'module' => array(),
     )
);
?>