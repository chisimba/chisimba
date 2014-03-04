<?php
/**
*
* A sample SQL file for simplefeedback. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_simplefeedback_surveys';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of survey info for simplefeedback', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
            'type' => 'text',
            'length' => 32
            ),
        'userid' => array(
            'type' => 'text',
            'length' => 25,
            'notnull' => TRUE,
            ),
	'datecreated' => array(
		'type' => 'timestamp'
            ),
        'title' => array(
            'type' => 'text',
            'length' => 250,
            'notnull' => TRUE,
            ),
	'description' => array(
            'type' => 'clob',
            )
	);

//create other indexes here...

$name = 'tbl_simplefeedback_surveys_idx';

$indexes = array(
    'fields' => array(
         'title' => array(),
    )
);
?>