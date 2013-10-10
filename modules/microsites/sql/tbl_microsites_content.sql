<?php
//Table Name
$tablename = 'tbl_microsites_content';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold content for the microsites', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'site_id' => array(
		'type'=>'text',
		'length'=> 32
		),
	'content' => array(
		'type'=>'clob',
		),
    'content_title' => array(
	'type' => 'text',
	'length' => 150,	
	),
    'hits' => array(
        'type' => 'integer',
        'length' => 4
        ),
	'modified' => array(
		'type' => 'timestamp'
		)
	);
?>
