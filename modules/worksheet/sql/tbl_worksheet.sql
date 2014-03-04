<?php
//Table Name
$tablename = 'tbl_worksheet';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of worksheets in a context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'context' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1,
		'default' => ''
		),
	'chapter' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1,
		'default' => ''
		),
	'activity_status' => array(
		'type' => 'text',
		'length' => 8,
		'notnull' => 1,
		'default' => 'inactive'
		),
	'percentage' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => 1,
		'default' => '0'
		),
	'total_mark' => array(
		'type' => 'integer',
		'notnull' => 1,
		'default' => '0'
		),
	'closing_date' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	'description' => array(
		'type' => 'text'
		),
	'userid' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1,
		'default' => ''
		),
	'last_modified' => array(
		'type' => 'timestamp',
		'notnull' => 1,
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14,
		'notnull' => 1
		)
	);
// Other indicies
$name = 'contextx';
$indexes = array(
    'fields' => array(
        'context' => array()
    )
);
?>