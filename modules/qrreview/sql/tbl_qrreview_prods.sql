<?php
// Table Name
$tablename = 'tbl_qrreview_prods';

//Options line for comments, encoding and character set
$options = array('comment' => 'products to be reviewed', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'farmid' => array(
        'type' => 'text', 
        'length' => 50,
        ),
	'longdesc' => array(
		'type' => 'clob',
		),
	'prodname' => array(
		'type' => 'text',
		'length' => 255,
		),
    'qr' => array(
        'type' => 'text',
        'length' => 255,
       ),
    'score' => array(
        'type' => 'integer', 
       ),
    'numrev' => array(
        'type' => 'integer',
       ),	
    'aggregate' => array(
        'type' => 'integer',
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
