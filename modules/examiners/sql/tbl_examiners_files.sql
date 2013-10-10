<?php
// Table Name
$tablename = 'tbl_examiners_files';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the file details for each subject.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'fac_id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'dep_id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'subj_id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'file_name' => array(
        'type' => 'text',
        'length' => '255',
    ),
	'file_type' => array(
		'type' => 'text',
		'length' => '255',
	),
    'file_date' => array(
        'type' => 'timestamp',
    ),
    'file_version' => array(
        'type' => 'timestamp',
    ),
	'deleted' => array(
		'type' => 'integer', 
		'length' => '1',
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>