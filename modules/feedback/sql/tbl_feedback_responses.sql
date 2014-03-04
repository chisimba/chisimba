<?php
// Table Name
$tablename = 'tbl_feedback_responses';

//Options line for comments, encoding and character set
$options = array('comment' => 'dfx comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'fb_response' => array(
		'type' => 'clob'
        ),
	'modified' => array(
		'type' => 'timestamp'
		),
    'resp_id'=>array('type' => 'integer',
                     'length'=>'20'
                    ),
    'question_id'=> array('type' => 'integer', 
                          'length'=>'20'
                    )
	);

//create other indexes here...

?>
