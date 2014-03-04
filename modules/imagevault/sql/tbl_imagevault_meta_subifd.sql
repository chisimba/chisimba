<?php
// Table Name
$tablename = 'tbl_imagevault_meta_subifd';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata subifd section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'blacklevelrepeatdim' => array(
		'type' => 'text',
		'length' => 100,
		),
    'blacklevel' => array(
		'type' => 'text',
		'length' => 100,
		),
	'whitelevel' => array(
		'type' => 'text',
		'length' => 100,
		),
	'defaultscale' => array(
		'type' => 'text',
		'length' => 100,
		),
	'defaultcroporigin' => array(
		'type' => 'text',
		'length' => 100,
		),
	'defaultcropsize' => array(
		'type' => 'text',
		'length' => 100,
		),
	'bayergreensplit' => array(
		'type' => 'text',
		'length' => 100,
		),
	'chromablurradius' => array(
		'type' => 'text',
		'length' => 100,
		),
	'antialiasstrength' => array(
		'type' => 'text',
		'length' => 100,
		),
	'bestqualityscale' => array(
		'type' => 'text',
		'length' => 100,
		),
	'activearea' => array(
		'type' => 'text',
		'length' => 100,
		),
	'maskedareas' => array(
		'type' => 'text',
		'length' => 100,
		),
	'noisereductionapplied' => array(
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
