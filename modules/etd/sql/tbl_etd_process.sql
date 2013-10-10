<?php

//5ive definition
$tablename = 'tbl_etd_process';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the steps for the submission process', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'step_num' => array(
		'type' => 'integer',
		'length' => 11,
		'unsigned' => 'true',
		'notnull' => 1,
		'default' => 0
		),
	'step_type' => array(
		'type' => 'text',
		'length' => 30
		),
	'group_name' => array(
		'type' => 'text',
		'length' => 30
		),
    'is_active' => array(
		'type' => 'integer',
		'length' => 11,
		'unsigned' => 'true',
		'notnull' => 1,
		'default' => 1
        ),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifier_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'etd_process_index';

$indexes = array(
                'fields' => array(
                	'step_num' => array(),
                	'step_type' => array()
                )
        );
?>