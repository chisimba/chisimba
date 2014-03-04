<?php
//Table Name
$tablename = 'tbl_assignment_workgroups';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of assignments that belong to workgroups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1,
		'default' => '',
		),

	'assignment_id' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		),

	'workgroup_id' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		)
	);
$name = 'workgroups_index';
$indexes = array(
    'fields' => array(
        'assignment_id' => array(),
        'workgroup_id' => array()
    )
);
?>
