<?php

$tablename = 'tbl_files_userfilters';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'filtername' => array(
        'type' => 'text',
        'length' => 40
        ),
    'filterdescription' => array(
        'type' => 'text',
        'length' => 255
        ),
    'userid' => array(
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

$name = 'index_tbl_files_userfilters';

$indexes = array(
                'fields' => array(
                    'userid' => array()
                )
        );

?>