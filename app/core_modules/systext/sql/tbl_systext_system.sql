<?php
// Table Name
$tablename = 'tbl_systext_system';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold system types for text abstraction', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
    'systemType' => array(
        'type' => 'text',
		'length' => 25
        ),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25,

		),
    'dateCreated' => array(
		'type' => 'date',

		),
    'canDelete' => array(
		'type' => 'text',
        'length' => 3
		)
    );

//create other indexes here...

$name = 'creatorId';

$indexes = array(
                'fields' => array(
                	'creatorId' => array(),
                    'systemType' => array()
                )
        );

?>