<?php

$tablename = 'tbl_contextcontent_chaptercontext';

// Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'chapterid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'chapterorder' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        ),
    'visibility' => array(
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
        ),
    'scorm' => array(
        'type' => 'text',
        'length' => 1,
        'default' => 'N'
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
   'releasedate' => array(
        'type' => 'timestamp'
        ),
   'enddate' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_contextcontent_chaptercontext_idx';

$indexes = array(
    'fields' => array(
        'contextcode' => array(),
        'chapterorder' => array(),
        'visibility' => array()
        )
    );

?>
