<?php
/**
*
* A sample SQL file for simplefeedback. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_simplefeedback_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of text for the simplefeedback questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
        'userid' => array(
            'type' => 'text',
            'length' => 25,
            'notnull' => TRUE,
            ),
	'datecreated' => array(
		'type' => 'timestamp'
            ),
        'questionno' => array(
            'type' => 'text',
            'length' => 10,
            'notnull' => TRUE,
            ),
	'question' => array(
            'type' => 'text',
            'length' => 250,
            )
	);

//create other indexes here...

$name = 'tbl_simplefeedback_questions_idx';

$indexes = array(
    'fields' => array(
         'question' => array(),
    )
);
?>