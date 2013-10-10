<?php
// Table Name
$tablename = 'tbl_cms_menustyles';

//Options line for comments, encoding and character set
$options = array('comment' => 'The different styles of menu that can be used', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'menu_style' => array(
        'type' => 'text',
        'length' => 32
        ),
    'root_nodes' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => 1,
        'default' => 0
        ),
    'is_active' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => 1,
        'default' => 0
        ),
    'updated' => array(
		'type' => 'timestamp'
		),
    'editable' => array(
        'type' => 'integer',
        'length' => 1
        )

    );

//create other indexes here...

$name = 'cms_menustyles_index';

$indexes = array(
                'fields' => array(
                	'is_active' => array()
                )
        );

?>
