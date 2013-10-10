<?php
// Table Name
$tablename = 'tbl_imagevault_images';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault files', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'filename' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'thumbnail' => array(
	    'type' => 'clob',
	    ),
	'dateuploaded' => array(
	    'type' => 'timestamp',
	    ),
	'hash' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'license' => array(
	    'type' => 'text',
	    'length' => 50,
	    ),
	'metadataid' => array(
	    'type' => 'text',
	    'length' => 32,
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
