<?php
$tablename = 'tbl_cms_content_frontpage';

$options = array('comment' => 'cms front page','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'content_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'show_content' => array(
		'type' => 'integer',
		'length' => 2,
		'notnull' => 1,
		'default' => 0
		),
	'ordering' => array(
		'type' => 'text',
		'length' => 11,
        'notnull' => TRUE,
        'default' => 0
		),
	'public_access' => array(
		'type' => 'boolean',
		'default' => '1'
		)		

    );

$name = 'content_id';

$indexes = array(
                'fields' => array(
                	'content_id' => array()
                )
        );
?>
