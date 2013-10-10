<?php
// Table Name
$tablename = 'tbl_foaf_myfoaf';

//Options line for comments, encoding and character set
$options = array('comment' => 'Base table for FOAF', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'homepage' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'weblog' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'page' => array(
        'type' => 'integer',
        'length' => 20,
        ),
    'phone' => array(
        'type' => 'text',
        'length' => 30,
        ),
    'jabberid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'theme' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'onlineacc' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'onlinegamingacc' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'workhomepage' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'schoolhomepage' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'interest' => array(
        'type' => 'integer',
        'length' => 25,
        ),
    'fundedby' => array(
        'type' => 'integer',
        'length' => 25,
        ),
    'logo' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'basednear' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'depiction' => array(
        'type' => 'integer',
        'length' => 25,
        ),
    'organizations' => array(
        'type' => 'integer',
        'length' => 25,
        ),
    'geekcode' => array(
        'type' => 'clob'
        ),


    );

    //create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                    'userid' => array(),
                )
        );
?>