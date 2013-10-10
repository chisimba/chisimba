<?php
// Table Name
$tablename = 'tbl_dfx_feedback';

//Options line for comments, encoding and character set
$options = array('comment' => 'dfx comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'fbname' => array(
		'type' => 'text',
		'length' => 255,
		),
	'fbemail' => array(
		'type' => 'text',
		'length' => 255,
		),
	'fbww' => array(
		'type' => 'clob',
		),
	'fbnw' => array(
		'type' => 'clob',
		),
	'fblo' => array(
		'type' => 'clob',
		),
	'fbsp' => array(
		'type' => 'clob',
		),
	'fbee' => array(
		'type' => 'clob',
		),
	'fbw' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>