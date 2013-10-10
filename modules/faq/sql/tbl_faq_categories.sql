<?php
//table name
$tablename = 'tbl_faq_categories';

//options array
$options = array('comment' => 'faq','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//fields
$fields = array(
	'id' =>array(
		'type' => 'text',
		'length' => 32,
		'notnull' => TRUE,
		'default' => '',
		 ),
	'categoryname' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => TRUE,
		'default' => '',
		 ),	
	'contextid' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => TRUE,
		'default' => '',
		 ),
	'userid' => array(
	  	'type' => 'text',
		'length' => 25,
		'notnull' => TRUE,
		'default' => '',
		),		  
	'datelastupdated' => array(
		'type' => 'timestamp',
		'notnull' => TRUE,
		'default' => '0000-00-00 00:00:00',
		),
	'updated' => array(
		'type' => 'timestamp',
		'notnull' => TRUE,
		'default' => '0000-00-00 00:00:00'
		)
	);
?>
