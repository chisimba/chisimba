<?php
    
//5ive definition
$tablename = 'tbl_etd_degrees';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table listing the degrees and faculties', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 255
		),
	'type' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => 1,
		'default' => 'faculty'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'etd_degrees_id';

$indexes = array(
                'fields' => array(
                	'type' => array()
                )
        );
?>