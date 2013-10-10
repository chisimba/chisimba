<?php
$tablename = 'tbl_cms_templates';
$options = array('comment' => 'cms_templates', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
    'image' => array(
        'type' => 'text',
        'length' => 255
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255
        ),
    'body' => array(
		'type' => 'clob',
		),
    'published' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
	'trash' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
	'created' => array(
		'type' => 'timestamp',

		),
    'created_by' => array(
		'type' => 'text',
        'length' => 32,
		)
	);

//create other indexes here...

$name = 'idx_templates';

$indexes = array(
                'fields' => array(
                	'title' => array()
                )
        );
?>
