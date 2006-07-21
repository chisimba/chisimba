<?php
// Table Name
$tablename = 'tbl_systext_abstract';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of text items to be abstracted', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
    'systemId' => array(
        'type' => 'text',
		'length' => 32,

        ),
    'textId' => array (
        'type' => 'text',
        'length' => 32,

        ),
    'abstract' => array (
        'type' => 'text',
        'length' => 50,

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
                	'systemId' => array(),
                    'textId' => array(),
                    'creatorId' => array()
                )
        );




?>