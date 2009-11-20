<?php
// Table Name
$tablename = 'tbl_stories';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to hold stories as elements of text for display', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'category' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        'default' => 'hidden'
        ),
    'isactive' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
        ),
    'parentid' => array(
        'type' => 'text',
        'length' => 32,
        'default' => 'base'
        ),
    'language' => array(
        'type' => 'text',
        'length' => 2,
        'default' => 'en'
        ),
    'title' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'abstract' => array(
        'type' => 'text'
        ),
    'maintext' => array(
        'type' => 'text'
        ),
    'datecreated' => array(
        'type' => 'date'
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25
        ),
    'expirationdate' => array(
        'type' => 'date'
        ),
    'notificationdate' => array(
        'type' => 'date'
        ),
    'issticky' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
        ),
    'modified' => array(
        'type' => 'timestamp'
        ),
    'datemodified' => array(
        'type' => 'date'
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25
        ),
    'commentcount' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        )
    );
?>