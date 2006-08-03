<?php
// Table Name
$tablename = 'tbl_loggedinusers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table is used to maintain state and enable communication', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 11,

        'autoincrement' => TRUE
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25,


		),
    'ipAddress' => array(
		'type' => 'text',
        'length' => 100,

		),
    'sessionId' => array(
		'type' => 'text',
        'length' => 100,

		),
    'whenLoggedIn' => array(
		'type' => 'date',


		),
    'WhenLastActive' => array(
		'type' => 'date',


		),
    'isInvisible' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'coursecode' => array(
		'type' => 'text',
        'length' => 100,

		),
    'themeUsed' => array(
		'type' => 'text',
        'length' => 100,

		)
    );

?>