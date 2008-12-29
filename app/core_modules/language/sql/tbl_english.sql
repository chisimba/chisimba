<?php

// Table Name
$tablename = 'tbl_en';

//Options line for comments, encoding and character set
$options = array('comment' => 'English language table','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 50,

        ),
    'en' => array(
        'type' => 'clob',
        //'length' => 255,
        ),
    'pageId' => array(
        'type' => 'text',
        'length' => 150
        ),
    'isInNextGen' => array(
        'type' => 'text',
        'length' => 10
        ),
    'dateCreated' => array(
        'type' => 'date'
        ),
    'creatorUserId' => array(
        'type' => 'text',
        'length' => 25,

        ),
    'dateLastModified' => array(
        'type' => 'date'
        ),
    'modifiedByUserId' => array(
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