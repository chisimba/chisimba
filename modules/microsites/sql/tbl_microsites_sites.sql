<?php
//Table Name
$tablename = 'tbl_microsites_sites';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold information about the different for the microsites', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'userid' => array(
		'type'=>'text',
		'length'=> 32
		),
	'site_name' => array(
		'type'=>'text',
		'length'=> 150
		),
    'url' => array(
	'type' => 'text',
	'length' => 250,	
	)
	);
?>
