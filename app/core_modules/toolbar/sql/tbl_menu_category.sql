<?php
// Table Name
$tablename = 'tbl_menu_category';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the items for the top toolbar / menu.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
	'category' => array(
		'type' => 'text',
		'length' => 120
		),
    'module' => array(
		'type' => 'text',
        'length' => 60
		),
    'adminonly' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'permissions' => array(
		'type' => 'text',
        'length' => 120
		),
    'dependscontext' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'contextplugin' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    );
?>