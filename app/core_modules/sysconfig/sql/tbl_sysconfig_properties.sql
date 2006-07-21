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


		),
    'pname' => array(
		'type' => 'text',
        'length' => 32,


		),
    'pvalue' => array(
		'type' => 'text',
        'length' => 32,


		),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25
		),
    'dateCreated' => array(
		'type' => 'date',


		),
    'modifierId' => array(
		'type' => 'text',
        'length' => 25
		),
    'dateModified' => array(
		'type' => 'date'
		)
    );


?>