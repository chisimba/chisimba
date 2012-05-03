<?php
$tablename = 'tbl_files';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'filename' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'datatype' => array(
        'type' => 'text',
        'length' => 255
        ),
    'path' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'filefolder' => array(
        'type' => 'clob',
        'notnull' => TRUE
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255
        ),
    'version' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 1
        ),
    'filesize' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE
        ),
    'mimetype' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
    'category' => array(
        'type' => 'text',
        'length' => 255
        ),
    'license' => array(
        'type' => 'text',
        'length' => 32
        ),
    'moduleuploaded' => array(
        'type' => 'text',
        'length' => 255
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
        ),
,
    'visibility' => array(
        'type' => 'text',
        'length' => 20
        ),
    'digitallibrary' => array(
        'type' => 'text',
        'length' => 1
        )
    );
//create other indexes here...

$name = 'index_tbl_files';

$indexes = array(
                'fields' => array(
                    'userid' => array(),
                    'filename' => array(),
                    'version' => array(),
                    'filesize' => array(),
                    'mimetype' => array(),
                    'category' => array(),
                    'creatorid' => array(),
                    'modifierid' => array()
                )
        );

?>