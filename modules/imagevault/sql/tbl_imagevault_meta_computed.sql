<?php
// Table Name
$tablename = 'tbl_imagevault_meta_computed';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata computed section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');


// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'imageid' => array(
		'type' => 'text',
		'length' => 32
		),	
    'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'html' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'height' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'width' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'iscolor' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'byteordermotorola' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'ccdwidth' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'aperturefnumber' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'thumbnail_filetype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'thumbnail_mimetype' => array(
        'type' => 'text',
        'length' => 100,
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
