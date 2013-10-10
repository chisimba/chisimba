<?php
// Table Name
$tablename = 'tbl_examiners_audit';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold examiners audit data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'table_name' => array(
        'type' => 'text',
        'length' => '255',
    ),
	'record_id' => array(
		'type' => 'text',
		'length' => '32',
	),
    'field_name' => array(
        'type' => 'text',
        'length' => '255',
    ),
    'old_value' => array(
        'type' => 'clob',
    ),
    'new_value' => array(
        'type' => 'clob',
    ),
    'trans_type' => array(
        'type' => 'text',
        'length' => '6',
    ),
	'modifier_id' => array(
        'type' => 'text',
        'length' => '32',
	),
	'date_modified' => array(
		'type' => 'timestamp', 
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>