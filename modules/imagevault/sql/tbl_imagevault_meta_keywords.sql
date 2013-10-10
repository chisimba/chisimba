<?php
// Table Name
$tablename = 'tbl_imagevault_meta_keywords';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault keywords', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'imageid' => array(
	    'type' => 'text',
	    'length' => 32
	    ),
	'keyword' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'datecreated' => array(
	    'type' => 'text',
	    'length' => 255,
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
