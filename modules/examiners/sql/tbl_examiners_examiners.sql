<?php
// Table Name
$tablename = 'tbl_examiners_examiners';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the examiners per faculty.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'title' => array(
		'type' => 'text',
		'length' => '15',
	),
	'first_name' => array(
        'type' => 'text',
        'length' => '255',
    ),
	'surname' => array(
		'type' => 'text',
		'length' => '255',
	),
    'organisation' => array(
        'type' => 'text',
        'length' => '255',
    ),
    'email_address' => array(
        'type' => 'text',
        'length' => '255',
    ),
    'tel_no' => array(
        'type' => 'text',
        'length' => '20',
    ),
    'extension' => array(
        'type' => 'integer',
        'length' => '4',
    ),
    'cell_no' => array(
        'type' => 'text',
        'length' => '20',
    ),
    'address' => array(
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