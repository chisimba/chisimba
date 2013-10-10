<?php
// Table Name
$tablename = 'tbl_qrmsgs';

//Options line for comments, encoding and character set
$options = array('comment' => 'QR code messages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'msg' => array(
		'type' => 'clob',
		),
	'lat' => array(
		'type' => 'text',
		'length' => 255,
		),	
	'lon' => array(
		'type' => 'text',
		'length' => 255,
		),	
	'gmapsurl' => array(
		'type' => 'clob',
		),
	'creationdate' => array(
		'type' => 'text',
		'length' => 80,
		),		
	);
//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>
