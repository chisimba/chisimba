<?php

// Table Name
$tablename = 'tbl_sysconfig_properties';

//Options line for comments, encoding and character set
$options = array('comment' => 'system properties', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'pmodule' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => 1,
        'default' => 'unknown'
        ),
    'pname' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1,
        'default' => 'novalue'
        ),
    'pvalue' => array(
        'type' => 'clob',
        'notnull' => 0,
        'default' => ''
        ),
    'pdesc' => array(
        'type' => 'clob'
        ),
    'creatorId' => array(
        'type' => 'text',
        'length' => 25
        ),
    'dateCreated' => array(
        'type' => 'timestamp',
        ),
    'modifierId' => array(
        'type' => 'text',
        'length' => 25
        ),
    'dateModified' => array(
        'type' => 'timestamp'
        )
    );


?>