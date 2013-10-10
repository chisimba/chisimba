<?php

$tablename = 'tbl_ahis_geography_level2';

$options = array('comment'=> 'table to store geography segement level 2 info','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'geo3id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'name' => array(
        'type' => 'text',
        'length' => 64,
        'notnull' => TRUE
        ),
    'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
    'datecreated' => array(
		'type' => 'timestamp'
		),
    'modifierid' => array(
		'type' => 'integer',
		'length' => 32
		),
    'datemodified' => array(
		'type' => 'timestamp'
		)
    );
//create other indexes here...

$name = 'index_tbl_ahis_geography_level2';

$indexes = array(
                'fields' => array(
                	'geo3id' => array()
                )
        );

?>