<?php

$tablename = 'tbl_dynamicblocks';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE,
		),
	'module' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
	'object' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'function' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'parameter' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'title' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
	'typeofblock' => array(
		'type' => 'text',
		'length' => 10,
        'notnull' => TRUE,
		'default' => 'context'
		),
    'userorcontextorworkgroupcode' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => FALSE
		),
    'blocksize' => array(
		'type' => 'text',
		'length' => 5,
        'notnull' => TRUE,
        'default' => 'small'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE,
		),
	'datecreated' => array(
		'type' => 'timestamp',
		),
    );
    
$name = 'tbl_dynamicblocks_index';

$indexes = array(
                'fields' => array(
                	'module' => array(),
                	'object' => array(),                    
                	'function' => array(),                    
                	'parameter' => array(),                    
                	'title' => array(),                    
                	'typeofblock' => array(),                    
                	'userorcontextorworkgroupcode' => array(),                    
                	'blocksize' => array(),                    
                )
        );
?>