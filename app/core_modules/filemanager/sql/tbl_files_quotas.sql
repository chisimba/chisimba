<?php

$tablename = 'tbl_files_quotas';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'path' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'usedefault' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 'Y'
        ),
    'quota' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        ),
    'quotausage' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
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

$name = 'index_tbl_files_quotas';

$indexes = array(
                'fields' => array(
                    'path' => array(),
                    'quota' => array(),
                    'quotausage' => array(),
                    'usedefault' => array(),
                    'creatorid' => array(),
                    'datecreated' => array(),
                    'modifierid' => array(),
                    'datemodified' => array(),
                )
        );

?>