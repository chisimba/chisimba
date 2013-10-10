<?php
//Table Name
$tablename = 'tbl_worksheet_results';

//Options line for comments, encoding and character set
$options = array('comment' => 'Results of worksheets completed by students', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'worksheet_id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'completed' => array(
		'type' => 'text',
		'length' => 1,
		'notnull' => 1,
		'default' => 'N'
		),
	'mark' => array(
		'type' => 'integer',
		'notnull' => 1,
		'default' => '-1'
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'last_modified' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	'updated' => array(
		'type' => 'timestamp',
		'notnull' => 1
		)
	);
// Other indicies
$name = 'worksheet_idx';
$indexes = array(
    'fields' => array(
        'worksheet_id' => array()
    )
);
?>