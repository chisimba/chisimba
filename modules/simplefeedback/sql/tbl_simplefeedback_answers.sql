<?php
/**
*
* A sample SQL file for simplefeedback. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_simplefeedback_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of answers for the simplefeedback module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'surveyid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'fullname' => array(
                'type' => 'text',
                'length' => 50,
		),
	'email' => array(
                'type' => 'text',
                'length' => 50,
		),
        'questionno' => array(
            'type' => 'text',
            'length' => 10,
            'notnull' => TRUE,
            ),
	'answer' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'tbl_simplefeedback_answers_idx';

$indexes = array(
    'fields' => array(
        'questionno' => array(),
        'fullname' => array(),
        'email' => array(),
    )
);
?>