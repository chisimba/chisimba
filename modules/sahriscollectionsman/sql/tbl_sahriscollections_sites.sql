<?php
// Table Name
$tablename = 'tbl_sahriscollections_sites';

//Options line for comments, encoding and character set
$options = array('comment' => 'SAHRIS Sites', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'sitename' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'siteabbr' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'sitemanager' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'sitecontact' => array(
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
	'comment' => array(
	    'type' => 'clob',
	    ),
	'datecreated' => array(
	    'type' => 'timestamp',
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
