<?php
// Table Name
$tablename = 'tbl_liftclub_details';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'times' => array(
		'type' => 'text',
		'length' => 20,
		),
	'additionalinfo' => array(
		'type' => 'text',
		'length' => 500,
		),	
	'specialoffer' => array(
		'type' => 'text',
		'length' => 2,
		),
	'emailnotifications' => array(
		'type' => 'text',
		'length' => 2,
		),
	'daysvary' => array(
		'type' => 'text',
		'length' => 2,
		),
	'smoke' => array(
		'type' => 'text',
		'length' => 2,
		),
		'userneed' => array(
		'type' => 'text',
		'length' => 15,
		),
		'needtype' => array(
		'type' => 'text',
		'length' => 15,
		),
		'daterequired' => array(
		'type' => 'date',
		),
		'createdormodified' => array(
		'type' => 'timestamp',
		),
	'monday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'tuesday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'wednesday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'thursday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'friday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'saturday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'sunday' => array(
		'type' => 'text',
		'length' => 2,
		),
	'safetyterms' => array(
		'type' => 'text',
		'length' => 2,
		)
	);
?>
