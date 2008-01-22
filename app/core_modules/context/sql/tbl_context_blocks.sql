<?php

//Table Name
$tablename = 'tbl_context_blocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table holds data pertaining to the blocks displayed on a context home page', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
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
    'updatedby' => array(
        'type' => 'text',
        'length' => 25
        )
    );
?>