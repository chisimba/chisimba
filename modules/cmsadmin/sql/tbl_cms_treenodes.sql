<?php

// Table Name
$tablename = 'tbl_cms_treenodes';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores tree and menu nodes for cms.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'node_type' => array(
		'type' => 'text',
		'length' => 32
		),
    'title' => array(
		'type' => 'text',
		'length' => 255
		),
    'link_reference' => array(
		'type' => 'text',
		'length' => 255
		),
    'banner' => array(
		'type' => 'text',
		'length' => 255
		),
    'parent_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'layout' => array(
		'type' => 'text',
		'length' => 255
		),
    'css' => array(
		'type' => 'text',
		'length' => 255
		),
    'ordering' => array(
		'type' => 'integer',
		'length' => 20
		),
    'published' => array(
		'type' => 'integer',
       		'length' => 1,
	        'notnull' => TRUE,
	        'default' => '0'
		),
    'publisher_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'artifact_id' => array(
		'type' => 'text',
		'length' => 32
		)
		
    );

?>