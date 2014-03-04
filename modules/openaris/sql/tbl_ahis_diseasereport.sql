<?php

$tablename = 'tbl_ahis_diseasereport';

$options = array('comment'=> 'table to store disease report screen 1 data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'reporterid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dataentryid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'validaterid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'countryid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'partitionid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'reportdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'prepareddate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'ibarsubdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	'ibarrecdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
    'validated' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
        ),
	'comments' => array(
		'type' => 'clob'
		),
    'outbreakcode' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'diseaseid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'occurenceid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'infectionid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'observationdate' => array(
		'type' => 'date'
		),
	'vetdate' => array(
		'type' => 'date'
		),
	'investigationdate' => array(
		'type' => 'date'
		),
	'samplesubdate' => array(
		'type' => 'date'
		),
	'diagnosisdate' => array(
		'type' => 'date'
		),
	'interventiondate' => array(
		'type' => 'date'
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

$name = 'index_tbl_ahis_diseasereport';

?>