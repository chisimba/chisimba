<?php
// Table Name
$tablename = 'tbl_imagevault_meta_file';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata file section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');


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
	'filename' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'filedatetime' => array(
        'type' => 'text',
        ),
    'filesize' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'filetype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'mimetype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'sectionsfound' => array(
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
