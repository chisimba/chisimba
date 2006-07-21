<?php
// Table Name
$tablename = 'tbl_modules';

//Options line for comments, encoding and character set
$options = array('comment' => 'modules', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'module_id' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
		),
    'module_authors' => array(
		'type' => 'text',
		'length' => 255,
		),
    'module_releasedate' => array(
		'type' => 'date'
		),
    'module_version' => array(
		'type' => 'text',
        'length' => 20
		),
    'module_path' => array(
		'type' => 'text',
		'length' => 255
		),
    'isAdmin' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'isVisible' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '1'
		),
    'hasAdminPage' => array(
		'type' => 'integer',
        'length' => 1,
        'default' => 1
		),
    'isContextAware' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'dependsContext' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		)
    );

?>