<?php
$tablename = 'tbl_forms';
$options = array('comment' => 'Forms collection management table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
	),
    'name' => array(
        'type' => 'text',
        'length' => 50
    ),
    'method' => array(
        'type' => 'text',
        'length' => 50
    ),
    'title' => array(
        'type' => 'text',
        'length' => 50
    ),
	'description' => array(
		'type' => 'text',
		'length' => 250
	),
    'width' => array(
        'type' => 'text',
        'length' => 50
    ),
    'height' => array(
        'type' => 'text',
        'length' => 50
    ),
    'css_class' => array(
        'type' => 'text',
        'length' => 255
    ),
    'script' => array(
        'type' => 'clob',
    ),
    'body' => array(
        'type' => 'clob'
    ),  
    'published' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
        ),
    'created_by' => array(
        'type' => 'text',
        'length' => 32,
        ),
	'updated' => array(
		'type' => 'timestamp'
	)
);

//create other indexes here...

$name = 'idx_forms';

$indexes = array(
                'fields' => array(
                    'name' => array()
                )
        );
?>
