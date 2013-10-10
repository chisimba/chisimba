<?php
// Table Name
$tablename = 'tbl_unihr_acctype';

//Options line for comments, encoding and character set
$options = array('comment' => 'Accommodation type', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'acc_type' => array(
			'type' => 'text',
			'length' => 50,
			),
	);

//create other indexes here...
$name = 'name';

$indexes = array(
                'fields' => array(
                	'acc_type' => array(),
                )
        );
?>