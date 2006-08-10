<?php
// Table Name

$tablename = 'tbl_userloginhistory';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table is used to maintain state and enable communication', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
	'userId' => array(
		'type' => 'text',
		'length' => 25,


		),
    'lastLoginDateTime' => array(
		'type' => 'date',


		)
    );

//create other indexes here...

$name = 'userId';

$indexes = array(
                'fields' => array(
                	'userId' => array()
                )
        );
?>