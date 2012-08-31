<?php
// Table Name
$tablename = 'tbl_login_nonce';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of login nonces', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'ipaddress' => array(
		'type' => 'text',
		'length' => 250,
		),
	'sessionid' => array(
		'type' => 'text',
		'length' => 250,
		),
	'nonce' => array(
		'type' => 'text',
		'length' => 250,
		),
	'tries' => array(
		'type' => 'integer',
		'length' => 50,
		),
        'enabled' => array(
                'type' => 'boolean',
                ),
	);

//create other indexes here...

$name = 'tbl_login_nonce_idx';

$indexes = array(
    'fields' => array(
         'ipaddress' => array(),
    )
);
?>