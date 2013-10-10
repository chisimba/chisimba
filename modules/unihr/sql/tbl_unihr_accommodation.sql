<?php
// Table Name
$tablename = 'tbl_unihr_accommodation';

//Options line for comments, encoding and character set
$options = array('comment' => 'Staff accommodation details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'acctype_id' => array(
		'type' => 'text',
		'length' => 255,
		),
	'acc_address' => array(
		'type' => 'clob',
		),
	'accstatus_id' => array(
		'type' => 'text',
		'length' => 255,
		),
	);

//create other indexes here...
$name = 'acc';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                	'acctype_id' => array(),
                	'accstatus_id' => array(),
                )
        );
?>