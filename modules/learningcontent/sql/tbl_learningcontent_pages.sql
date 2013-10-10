<?php

$tablename = 'tbl_learningcontent_pages';

// Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'titleid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'menutitle' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'pagepicture' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => FALSE
        ),
     'pageformula' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => FALSE
        ),
    'pagecontent' => array(
        'type' => 'text'
        ),
    'headerscripts' => array(
        'type' => 'text'
        ),
    'license' => array(
        'type' => 'text',
        'length' => 32
        ),
    'language' => array(
        'type' => 'text',
        'length' => 3,
        'notnull' => TRUE,
        'default' => 'en'
        ),
    'original' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 'N'
        ),
    'lockuser' => array(
        'type' => 'text',
        'length' => 25
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
    'scorm' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 'N'
        ),
    );
    
$name = 'tbl_contextcontent_pages_idx';

$indexes = array(
    'fields' => array(
        'titleid' => array(),
        'menutitle' => array(),
        'license' => array(),
        'language' => array(),
        'creatorid' => array(),
        'modifierid' => array()
        )
    );

?>
