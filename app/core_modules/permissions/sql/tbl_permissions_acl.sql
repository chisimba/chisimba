<?php

// Table Name
$tablename = 'tbl_permissions_acl';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores access control list for permissions.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'acl_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'group_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'last_updated' => array(
		'type' => 'date',
		),
    'last_updated_by' => array(
		'type' => 'text',
		'length' => 32
		)
    );

//create other indexes here...

$name = 'ind_acl_FK';

$indexes = array(
                'fields' => array(
                	'acl_id' => array(),
                    'group_id' => array(),
                    'user_id' => array()
                )
        );
?>