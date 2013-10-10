<?php
// Table Name
$tablename = 'tbl_cms_categories';

//Options line for comments, encoding and character set
$options = array('comment' => 'cms categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parent_id' => array(
		'type' => 'text',
		'length' => 32,
    'notnull' => TRUE,
		'default' => '0'
		),
    'title' => array(
		'type' => 'text',
		'length' => 50
		),
    'menutext' => array(
		'type' => 'text',
		'length' => 255
		),
    'image' => array(
		'type' => 'text',
		'length' => 100
		),
    'sectionid' => array(
		'type' => 'text',
		'length' => 50
		),
    'image_position' => array(
		'type' => 'text',
		'length' => 10
		),
    'description' => array(
		'type' => 'text',
		'length' => 255
		),
    'published' => array(
		'type' => 'integer',
		'length' => 1,
        'notnull' => TRUE,
		'default' => '0'
		),
    'checked_out' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE,
		'default' => '0'
		),
    'checked_out_time' => array(
		'type' => 'timestamp',


		),
    'editor' => array(
		'type' => 'text',
		'length' => 32
		),
    'ordering' => array(
		'type' => 'text',
		'length' => 32
		),
    'access' => array(
		'type' => 'text',
		'length' => 32
		),
    'count' => array(
		'type' => 'integer',
		'length' => 11,

		),
    'params' => array(
		'type' => 'text',
		'length' =>255
		)
    );

//create other indexes here...

$name = 'cat';

$indexes = array(
                'fields' => array(
                	'sectionid' => array(),
                	'published' => array(),
                	'access' => array(),
                    'checked_out' => array(),
                )
        );

?>
