<?php

/*
*Table to hold staff member details
*/

/*
*Set table name
*/
$tablename = 'tbl_rimfhe_staffmember';

/*
*options for comment 
*/
	
$options = array(
	'comment' => 'tbl_rimfhe_staffmember',
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
	'tiltle' => array(
		'type' => 'text',
		'length'=> 10,
		'notnull' => TRUE
		),
	'rank' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'appointmenttype' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'department' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'faculty' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'staffnumber' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'staffnumber' => array(
		'type' => 'text',
		'length'=> 150,
		'notnull' => TRUE
		),
	'email' => array(
		'type' => 'text',
		'length'=> 150,
		'notnull' => TRUE
		),
	'dateresistered' => array(
		'type' => 'timestamp',
		'notnull' => TRUE
		)	
	);
/*
*index
*/
$name = 'tbl_rimfhe_staffmember_idx';	

$indexes = array(
                'fields' => array(
				'staffnumber' => array()
		)
	);
?>
