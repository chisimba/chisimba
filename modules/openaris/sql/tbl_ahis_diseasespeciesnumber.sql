<?php

$tablename = 'tbl_ahis_diseasespeciesnumber';

$options = array('comment'=> 'table to store disease species data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'outbreakcode' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'speciesid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'agegroupid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'sexid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'risk' => array(
		'type' => 'integer'
        ),
	'cases' => array(
		'type' => 'integer'
        ),
	'deaths' => array(
		'type' => 'integer'
        ),
	'destroyed' => array(
		'type' => 'integer'
        ),
	'slaughtered' => array(
		'type' => 'integer'
        ),
	'date_created' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'date_modified' => array (
		'type' => 'date'
        ),	
	'modified_by' => array (
		'type' => 'text',
		'length' => 32
	)    
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_diseasespeciesnumber';

?>