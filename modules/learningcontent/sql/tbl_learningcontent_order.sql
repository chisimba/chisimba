<?php

$tablename = 'tbl_learningcontent_order';

// Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'titleid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'chapterid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'parentid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'lft' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        ),
    'rght' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        ),
    'pageorder' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        ),
    'visibility' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'datecreated' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25
        ),
    'datemodified' => array(
        'type' => 'timestamp'
        ),
    'bookmark' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'isbookmarked' => array(
        'type' => 'text',
        'length' => 32,
        )
    );
    
$name = 'tbl_contextcontent_order_idx';

$indexes = array(
    'fields' => array(
        'contextcode' => array(),
        'titleid' => array(),
        'parentid' => array(),
        'lft' => array(),
        'rght' => array(),
        'visibility' => array()
        )
    );

?>
