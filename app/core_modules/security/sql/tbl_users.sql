<?php

// Table Name
$tablename = 'tbl_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Primary user information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userId' => array(
        'type' => 'text',
        'length' => 25,


        ),
    'username' => array(
        'type' => 'text',
        'length' => 255,

        ),
    'title' => array(
        'type' => 'text',
        'length' => 25,

        ),
    'firstName' => array(
        'type' => 'text',
        'length' => 50,

        ),
    'surname' => array(
        'type' => 'text',
        'length' => 50,

        ),
    'pass' => array(
        'type' => 'text',
        'length' => 100,

        ),
    'creationDate' => array(
        'type' => 'date',

        ),
    'emailAddress' => array(
        'type' => 'text',
        'length' => 100,

        ),
    'logins' => array(
        'type' => 'text',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 0
        ),
    'sex' => array(
        'type' => 'text',
        'length' => 10
        ),
    'country' => array(
        'type' => 'text',
        'length' => 2
        ),
    'staffnumber' => array(
        'type' => 'text',
        'length' => 25
        ),
    'cellnumber' => array(
        'type' => 'text',
        'length' => 13
        ),
    'accesslevel' => array(
        'type' => 'text',
        'length' => 10,

        ),
    'isActive' => array(
        'type' => 'text',
        'length' => 10,

        ),
    'howCreated' => array(
        'type' => 'text',
        'length' => 32,
         'notnull' => TRUE,
        'default' => 'unknown'
        ),
    'updated' => array(
         'type' => 'date',


                ),
    'last_login' => array(
        'type' => 'timestamp',
    ),  
);


//create other indexes here...

$name = 'userId';

$indexes = array(
                'fields' => array(
                    'userId' => array()
                )
        );
?>
