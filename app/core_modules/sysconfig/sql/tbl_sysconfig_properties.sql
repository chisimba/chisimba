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
        'notnull' => TRUE,
        'default' => 'unknown'
		),
    'pname' => array(
		'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
        'default' => 'novalue'
		),
    'pvalue' => array(
		'type' => 'text',
        'length' => 256,
        'notnull' => TRUE,
        'default' => 'unknown'
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
