<?php
// Table Name
$tablename = 'tbl_examiners_blacklisting';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the examiner blacklisting.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'exam_id' => array(
	   'type' => 'text',
	   'length' => '32',
    ),
	'remarks' => array(
	   'type' => 'clob',
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