<?php

//5ive definition
$tablename = 'tbl_smssender_sentmessages';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => 1
		),
    'sender' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => 1
		),
    'recipientnumber' => array(
		'type' => 'text',
		'length' => 14,
        'notnull' => 1
		),
    'recipient' => array(
		'type' => 'text',
		'length' => 25
		),
    'message' => array(
		'type' => 'text',
        'notnull' => 1
		),
    'datesent' => array(
		'type' => 'timestamp',
        'notnull' => 1
		),
    'messageid' => array(
		'type' => 'text',
		'length' => 40
		),
    'result' => array(
		'type' => 'text',
        'notnull' => 1,
		'length' => 1,
        'default' => 'N'
		),
	);
    
//create other indexes here...

$name = 'tbl_tbl_smssender_sentmessages_idx';

$indexes = array(
                'fields' => array(
                	'sender' => array(),
                	'recipientnumber' => array(),
                	'recipient' => array(),
                    'datesent' => array(),
                    'messageid' => array(),
                    'result' => array(),
                )
        );
?>