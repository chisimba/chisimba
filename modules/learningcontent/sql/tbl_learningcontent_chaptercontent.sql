<?php

$tablename = 'tbl_learningcontent_chaptercontent';

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
    'chaptertitle' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'chapterpicture' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => FALSE
        ),
     'chapterformula' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => FALSE
        ),
    'introduction' => array(
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
        )
    );

$name = 'tbl_contextcontent_chaptercontent_idx';

$indexes = array(
    'fields' => array(
        'chapterid' => array(),
        'chaptertitle' => array(),
        'license' => array(),
        'language' => array(),
        'creatorid' => array(),
        'modifierid' => array()
        )
    );

?>
