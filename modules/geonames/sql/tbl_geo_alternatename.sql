<?php
// Table Name
$tablename = 'tbl_geo_alternatename';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geoname alternate names', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'alternatenameid' => array(
		'type' => 'integer',
		),
	'geonameid' => array(
		'type' => 'integer',
		),
	'isolanguage' => array(
		'type' => 'text',
		'length' => 7,
		),
	'alternatename' => array(
		'type' => 'text',
		'length' => 200,
		),
	'ispreferredname' => array(
		'type' => 'text',
		),
	'isshortname' => array(
		'type' => 'text',
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
