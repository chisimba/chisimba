<?php

//Table Name
$tablename = 'tbl_prelogin_blocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table holds data pertaining to the prelogin blocks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 35
        ),
    'title' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE,
        'default' => 'No Title'
        ),
    'side' => array(
        'type' => 'text',
        'length' => 10,
        'notnull' => TRUE,
        'default' => 'left'
        ),
    'visible' => array(
        'type' => 'boolean',
        'nonull' => TRUE,
        'default' => TRUE
        ),
    'position' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'isblock' => array(
        'type' => 'boolean',
        'notnull' => TRUE,
        'default' => FALSE
        ),
    'blockname' => array(
        'type' => 'text',
        'length' => 50
        ),
    'blockmodule' => array(
        'type' => 'text',
        'length' => 50
        ), 
    'content' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'datelastupdated' => array(
        'type' => 'date'
        ),
    'updatedby' => array(
        'type' => 'text',
        'length' => 25
        )
    );
?>