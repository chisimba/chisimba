<?php

$tablename = 'tbl_files_userfilters_file';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'filterid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'fileid' => array(
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
        'type' => 'date'
        ),
    'timecreated' => array(
        'type' => 'time'
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'datemodified' => array(
        'type' => 'date'
        ),
    'timemodified' => array(
        'type' => 'time'
        )
    );
//create other indexes here...

$name = 'index_tbl_files_userfilters_file';

$indexes = array(
                'fields' => array(
                    'filterid' => array(),
                    'fileid' => array()
                )
        );

?>