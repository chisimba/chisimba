<?php
$tablename = 'tbl_files_filetags';

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
    'tag' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        )
    );
//create other indexes here...

$name = 'index_tbl_files_filetags';

$indexes = array(
                'fields' => array(
                    'fileid' => array(),
                    'tag' => array()
                )
        );

?>