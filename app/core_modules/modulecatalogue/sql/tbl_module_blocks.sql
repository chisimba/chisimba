<?php
// Table Name
$tablename = 'tbl_module_blocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'table of module owned blocks','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'moduleid' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
		),
    'blockname' => array(
		'type' => 'text',
        'length' => 150,
		),
	'blockwidth' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => TRUE,
		'default' => 'normal'
		),
    'blocktype' => array(
		'type' => 'text',
        'length' => 150,
        'default' => 'site',
		)
    );

//create other indexes here...

$name = 'moduleid_index';

$indexes = array(
                'fields' => array(
                    'moduleid' => array(),
                    'blocktype' => array(),
                )
        );
?>
