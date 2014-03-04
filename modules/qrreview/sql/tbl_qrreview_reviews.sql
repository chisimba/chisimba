<?php
// Table Name
$tablename = 'tbl_qrreview_reviews';

//Options line for comments, encoding and character set
$options = array('comment' => 'Reviews and ratings table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'prodid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'prodrate' => array(
		'type' => 'integer',
		'length' => 10,
		),
	'prodcomm' => array(
		'type' => 'clob',
		),
    'phone' => array(
        'type' => 'text',
        'length' => 12,
    ),	
    'farmid' => array(
        'type' => 'text', 
        'length' => 50,
        ),
	'creationdate' => array(
		'type' => 'text',
		'length' => 80,
		),		
	);
//create other indexes here...

$name = 'prodid';

$indexes = array(
                'fields' => array(
                	'prodid' => array(),
                )
        );
?>
