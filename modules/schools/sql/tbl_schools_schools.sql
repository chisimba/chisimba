<?php
/**
*
* A sample SQL file for schools. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_schools_schools';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of detail for the schools module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
        'name' => array(
                'type' => 'text',
                'length' => 150
                ),
        'principal_id' => array(
                'type' => 'text',
                'length' => 32,
                ),
        'district_id' => array(
                'type' => 'text',
                'length' => 32,
                ),
        'address' => array(
                'type' => 'text',
                'length' => 250,
                ),
        'email_address' => array(
                'type' => 'text',
                'length' => 50,
                ),
       'telephone_number' => array(
                'type' => 'text',
                'length' => 20,
                ),
        'fax_number' => array(
                'type' => 'text',
                'length' => 20,
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

$name = 'tbl_schools_schools_idx';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'principal_id' => array(),
        'name' => array(),
        'district_id' => array(),
        'created_by' => array(),
        'modified_by' => array(),
    )
);
?>