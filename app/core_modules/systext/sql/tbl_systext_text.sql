<?php
// Table Name
$tablename = 'tbl_systext_text';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of text items to be abstracted', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
    'text' => array(
        'type' => 'text',
		'length' => 50
        ),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        'default' => '1'
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
                	'creatorId' => array()
                )
        );

?>