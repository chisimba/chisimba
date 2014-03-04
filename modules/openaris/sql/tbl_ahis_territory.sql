<?php

$tablename = 'tbl_ahis_territory';

$options = array('comment'=> 'Table to store territory information','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'name' => array(
        'type' => 'text',
        'length' => 64,
        'notnull' => TRUE
        ),
    'northlatitude' => array(
        'type' => 'text',
        'length' => 16,
        'notnull' => TRUE,
        'default' => 0
        ),
    'southlatitude' => array(
        'type' => 'text',
        'length' => 16,
        'notnull' => TRUE,
        'default' => 0
        ),
    'eastlongitude' => array(
        'type' => 'text',
        'length' => 16,
        'notnull' => TRUE,
        'default' => 0
        ),
    'westlongitude' => array(
        'type' => 'text',
        'length' => 16,
        'notnull' => TRUE,
        'default' => 0
        ),
    'geo2id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'area' => array(
        'type' => 'text',
        'length' => 64,
        'notnull' => TRUE
        ),
    'unitofmeasure' => array(
        'type' => 'text',
        'length' => 16,
        'notnull' => TRUE
        )
    );
        
    
//create other indexes here...

$name = 'index_tbl_ahis_territory';

?>