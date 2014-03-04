<?php
//5ive definition
$tablename = 'tbl_feedbackform';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for feedback_comment', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),


          'userid' => array(
		'type' => 'text',
		'length' => 32
		),
      
     'name' => array(
		'type' => 'text',
		'length' => 30
		),

     'email' => array(
		'type' => 'text',
		'length' =>60
		),
      'msgtxt' => array(
		'type' => 'clob'

		),
 'datemodified' => array(
		'type' => 'date'
		)
);

?>
