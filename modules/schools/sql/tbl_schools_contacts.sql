<?php
/**
*
* A sample SQL file for schools. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_schools_contacts';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of contacts for the schools module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
        'school_id' => array(
                'type' => 'text',
                'length' => 32,
                ),
        'position' => array(
                'type' => 'text',
                'length' => 150
                ),
        'name' => array(
                'type' => 'text',
                'length' => 150
                ),
        'address' => array(
                'type' => 'text',
                'length' => 250
                ),
        'email_address' => array(
                'type' => 'text',
                'length' => 150
                ),
        'telephone_number' => array(
                'type' => 'text',
                'length' => 25
                ),
        'mobile_number' => array(
                'type' => 'text',
                'length' => 25
                ),
        'fax_number' => array(
                'type' => 'text',
                'length' => 25
                ),
	'created_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'modified_by' => array(
		'type' => 'text',
		'length' => 32,
		),
	'date_modified' => array(
		'type' => 'timestamp'
		),
	);

//create other indexes here...

$name = 'tbl_schools_districts_idx';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'school_id' => array(),
    )
);
?>