<?php

//5ive definition
$tablename = 'tbl_profiles';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of user profiles', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),
	'profile' => array(
		'type' => 'clob'
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

$name = 'profiles_index';

$indexes = array(
                'fields' => array(
                	'userid' => array()
                )
        );
?>