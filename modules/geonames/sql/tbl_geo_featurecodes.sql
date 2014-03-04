<?php
// Table Name
$tablename = 'tbl_geo_featurecodes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Geo feature codes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'code' => array(
		'type' => 'text',
		'length' => 10,
		),
	'name' => array(
		'type' => 'text',
		'length' => 200,
		),	
	'description' => array(
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
