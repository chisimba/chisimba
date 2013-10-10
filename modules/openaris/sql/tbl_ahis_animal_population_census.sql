<?php

$tablename = 'tbl_ahis_animal_population_census';

$options = array('comment'=> 'table to store animal population information','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dataentryid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'vetofficerid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'repdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'reporterid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		
	'ibardate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'countryid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
	'year' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'partitiontypeid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'partitionlevelid' => array(
		'type' => 'integer',
		'length' => 1,
        'notnull' => TRUE
		),	
	'partitionnameid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'speciesid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'prodnameid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'breedid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'troplivestockid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'prodnumid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),	
	'breednoid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),		
	'crossbreednumid' => array(
		'type' => 'text',
		'length' => 32,
      'notnull' => TRUE
		),
    'animalcatid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE,
      ),
    'totnumid' => array(
        'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE
        ),
    'catnumid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
	'comments' => array(
		'type' => 'clob'
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

$name = 'index_tbl_ahis_animal_population_census';

?>
