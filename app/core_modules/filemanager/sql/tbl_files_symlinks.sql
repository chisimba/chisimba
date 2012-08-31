<?php

$tablename = 'tbl_files_symlinks';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'fileid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'folderid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'datecreated' => array(
        'type' => 'timestamp'
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'datemodified' => array(
        'type' => 'timestamp'
        ),
    );
//create other indexes here...

$name = 'index_tbl_files_symlinks';

$indexes = array(
                'fields' => array(
                    'fileid' => array(),
                    'folderid' => array(),
                    'creatorid' => array(),
                    'datecreated' => array(),
                    'modifierid' => array(),
                    'datemodified' => array(),
                )
        );

?>