<?php
//Table Name
$tablename = 'tbl_assignment_uploadablefiletypes';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of file types allowed for uploadable assignments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1,
		'default' => '',
		),
	'assignmentid' => array(
		'type' => 'text',
		'length' => 32
		),
	'filetype' => array(
		'type' => 'text',
		'length' => 64,
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1,
		),
	'last_modified' => array(
		'type' => 'timestamp',
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14,
		'notnull' => 1,
		),
	);
// Other indicies
$name = 'assignment_idx';
$indexes = array(
    'fields' => array(
        'assignmentid' => array()
    )
);
?>