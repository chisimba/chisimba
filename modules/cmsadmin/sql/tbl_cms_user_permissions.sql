<?php
//
// Table Name
$tablename = 'tbl_cms_user_permissions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Stores users and their respective extra permissions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'show_on_frontpage' => array(
		'type' => 'boolean'
		)
    );

$name = 'idx_cms_uperm';

$indexes = array(
                'fields' => array(
                	'user_id' => array(),
                )
        );


?>
