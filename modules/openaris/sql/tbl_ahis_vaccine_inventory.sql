<?php

$tablename = 'tbl_ahis_vaccine_inventory';

$options = array('comment'=> 'table to store vaccine inventory data','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'district' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'vaccinename' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'doses' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dosesstartofmonth' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'startmonth' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dosesendofmonth' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'endmonth' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dosesreceived' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'dosesused' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'doseswasted' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
		'reportdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		)
	
    );

?>