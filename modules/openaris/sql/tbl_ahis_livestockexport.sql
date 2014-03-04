<?php

// Table Name
$tablename = 'tbl_ahis_livestockexport';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the keywords for livestock export', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'district' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	
	'entrypoint' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	
	'origin' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	
	'destination' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'classification' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
	'eggs' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
	'milk' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
	'cheese' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
	'poultry' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
	'beef' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
    ),
    'count' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    )
);

?>