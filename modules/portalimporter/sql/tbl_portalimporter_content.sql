<?php
// Table Name
$tablename = 'tbl_portalimporter_content';

//Options line for comments, encoding and character set
$options = array('comment' => 'Portal content', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'filepath' => array(
		'type' => 'text',
		'length' => 255,
		),
	'filetype' => array(
		'type' => 'text',
		'length' => 10,
		),
	'portalpath' => array(
		'type' => 'text',
		'length' => 255,
		),
	'portal' => array(
		'type' => 'text',
		'length' => 255,
		),
	'section' => array(
		'type' => 'text',
		'length' => 255,
		),
	'subportal' => array(
		'type' => 'text',
		'length' => 255,
		),
	'page' => array(
		'type' => 'text',
		'length' => 255,
		),
	'pagetitle' => array(
		'type' => 'text',
		'length' => 255,
		),
	'structuredcontent' => array(
		'type' => 'clob',
		),
	'rawcontent' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'portalpath';

$indexes = array(
                'fields' => array(
                	'portalpath' => array(),
                )
        );
?>
