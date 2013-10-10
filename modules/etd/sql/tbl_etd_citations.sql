<?php

//5ive definition
$tablename = 'tbl_etd_citations';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of citations for a resource', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'submission_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'citation_list' => array(
		'type' => 'clob'
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

$name = 'etd_citations_index';

$indexes = array(
                'fields' => array(
                	'submission_id' => array()
                )
        );
?>