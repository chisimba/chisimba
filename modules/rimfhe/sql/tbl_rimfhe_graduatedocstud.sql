<?php

/*
*Table to hold book  details
*/

/*
*Set table name
*/
$tablename = 'tbl_rimfhe_graduatedocstud';

/*
*options for comment
*/
	
$options = array(
	'comment' => 'This table stores data of chapters books in tbl_rimfhe_graduatedocstud',
	'collate' => 'utf8_general_ci',
	'character_set' => 'utf8'
	);

/*
Create the table fields
*/

$fields = array(
	'id' => array(
		'type' => 'text',
		'length'=> 32,
		'notnull' => TRUE
		),
	'surname' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'initials' => array(
		'type' => 'text',
		'length'=> 10,
		'notnull' => TRUE
		),
	'firstname' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'gender' => array(
		'type' => 'text',
		'length'=> 6,
		'notnull' => TRUE
		),
	'regnumber' => array(
		'type' => 'text',
		'length'=> 40,
		'notnull' => TRUE
		),
	'deptschoool' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'faculty' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'thesistitle' => array(
		'type' => 'text',
		'notnull' => TRUE
		),
	'supervisorname' => array(
		'type' => 'text',
		'length'=> 300,
		'notnull' => TRUE
		),
	'degree' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		)
	);
/*
*index
*/
$name = 'tbl_rimfhe_graduatedocstud_idx';	

$indexes = array(
                'fields' => array(
				'regnumber' => array()
		)
	);
?>
