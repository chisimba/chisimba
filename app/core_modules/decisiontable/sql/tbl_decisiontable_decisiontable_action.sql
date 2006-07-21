<?php

// Table Name
$tablename = 'tbl_decisiontable_decisiontable_action';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of actions and decision tables.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'actionId' => array(
		'type' => 'text',
		'length' => 32,
        ),
    'decisiontableId' => array(
		'type' => 'text',
		'length' => 32,
        )
    );

//create other indexes here...

$name = 'decisiontableId';

$indexes = array(
                'fields' => array(
                	'decisiontableId' => array(),
                    'actionid' => array()
                )
        );
?>