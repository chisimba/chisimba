<?php
// Table Name
$tablename = 'tbl_imagevault_meta_ifd1';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata ifd1 (thumbnail) section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'imagelength' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'stripoffsets' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'stripbytecounts' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'ycbcrsubsampling' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'thumbnailoffset' => array(
        'type' => 'text',
        'length' => 100,
        ),  
    'thumbnaillength' => array(
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
