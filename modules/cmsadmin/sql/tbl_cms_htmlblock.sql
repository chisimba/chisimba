<?php
//5ive definition
$tablename = 'tbl_cms_htmlblock';

//Options line for comments, encoding and character set
$options = array('comment' => 'Block for user to use for anything, within / out of a context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'heading' => array(
		'type' => 'text',
		'length' => 100
		),
    'content' => array(
		'type' => 'clob'
		),
    'context_code' => array(
		'type' => 'text',
		'length' => '255'
		),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32
		),
    'modifier_id' => array(
		'type' => 'integer',
		'length' => 3
		),
    'date_created' => array(
		'type' => 'timestamp'
		),
    'updated' => array(
		'type' => 'timestamp'
		)
);

?>