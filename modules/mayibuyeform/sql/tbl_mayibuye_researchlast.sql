<?php
//5ive definition
$tablename = 'tbl_mayibuye_researchlast';

//Options line for comments, encoding and character set
$options = array('comment' => 'tbl_mayibuye_researchlast','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
      'userid'=> array(
	        'type' => 'text',
		'length' => 5
		),

 	'studentno'=>array(
	        'type' => 'text',
		'length' => 10
		),

	'staffno'=>array(
	        'type' => 'text',
		'length' => 10
		),

	'collection'=>array(
	        'type' => 'text',
		'length' => 150
		),

	'imageaudio'=>array(
	        'type' => 'text',
		'length' => 150
		),

         'projectname'=>array(
	        'type' => 'text',
		'length' => 150
		),

         'timeperido'=>array(
	        'type' => 'text',
		'length' => 10
		));
?>
