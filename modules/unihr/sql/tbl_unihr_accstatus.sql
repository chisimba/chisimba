<?php
// Table Name
$tablename = 'tbl_unihr_accstatus';

//Options line for comments, encoding and character set
$options = array('comment' => 'Accommodation status', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'acc_statusname' => array(
			'type' => 'text',
			'length' => 50,
			),
	);

//create other indexes here...
$name = 'name';

$indexes = array(
                'fields' => array(
                	'acc_statusname' => array(),
                )
        );
?>