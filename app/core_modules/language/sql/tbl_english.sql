<?php

// Table Name
$tablename = 'tbl_en';

//Options line for comments, encoding and character set
$options = array('comment' => 'English language table','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'clob',
        ),
    'en' => array(
        'type' => 'clob',
        ),
    'pageid' => array(
        'type' => 'text',
        'length' => 150
        ),
    'isinnextgen' => array(
        'type' => 'text',
        'length' => 10
        ),
    'datecreated' => array(
        'type' => 'date'
        ),
    'creatoruserid' => array(
        'type' => 'text',
        'length' => 25,

        ),
    'datelastmodified' => array(
        'type' => 'date'
        ),
    'modifiedbyuserid' => array(
        'type' => 'text',
        'length' => 25
        )
    );

//create other indexes here...

$name = 'eng_code';

$indexes = array(
                'fields' => array(
                    'id' => array()
                )
        );
?>