<?php
$tablename = 'tbl_podcaster_metadata_media';

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
    'filename' => array(
        'type' => 'text',
        'length' => 150,
        'notnull' => TRUE
        ),
    'uploadpathid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'width' => array(
        'type' => 'integer',
        'length' => 11,
        'default' => 0
        ),
    'height' => array(
        'type' => 'integer',
        'length' => 11,
        'default' => 0
        ),
    'playtime' => array(
        'type' => 'integer',
        'length' => 11,
        'default' => 0
        ),
    'format' => array(
        'type' => 'text',
        'length' => 30
        ),
    'mimetype' => array(
        'type' => 'text',
        'length' => 50
        ),
    'cclicense' => array(
        'type' => 'text',
        'length' => 150
        ),
    'framerate' => array(
        'type' => 'integer',
        'length' => 11
        ),
    'bitrate' => array(
        'type' => 'integer',
        'length' => 11
        ),
    'samplerate' => array(
        'type' => 'integer',
        'length' => 11
        ),
    'title' => array(
        'type' => 'text',
        'length' => 255
        ),
    'artist' => array(
        'type' => 'text',
        'length' => 255
        ),
    'description' => array(
        'type' => 'text'
        ),
    'year' => array(
        'type' => 'text',
        'length' => 10
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255
        ),
    'getid3info' => array(
        'type' => 'text'
        ),
    'publishstatus' => array(
        'type' => 'text',
        'length' => 10
        ),
    'access' => array(
        'type' => 'text',
        'length' => 50
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

$name = 'index_tbl_podcaster_metadata_media';

$indexes = array(
                'fields' => array(
                    'fileid' => array()
                )
        );
?>