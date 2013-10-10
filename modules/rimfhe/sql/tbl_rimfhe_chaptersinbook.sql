<?php

/*
*Table to hold book  details
*/

/*
*Set table name
*/
$tablename = 'tbl_rimfhe_chaptersinbook';

/*
*options for comment
*/
	
$options = array(
	'comment' => 'This table stores data of chapters books in tbl_rimfhe_chaptersinbook',
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
	'booktitle' => array(
		'type' => 'text',
		'length'=> 200,
		'notnull' => TRUE
		),
	'isbn' => array(
		'type' => 'text',
		'length'=> 20,
		'notnull' => TRUE
		),
	'bookeditors' => array(
		'type' => 'text',
		'length'=> 300,
		'notnull' => TRUE
		),
	'publisher' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'chaptertitle' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'authorname' => array(
		'type' => 'text',
		'length'=> 300,
		'notnull' => TRUE
		),
	'chapterfirstpageno' => array(
		'type' => 'integer',
		'notnull' => TRUE
		),
	'chapterlastpageno' => array(
		'type' => 'integer',
		'notnull' => TRUE
		),
	'pagetotal' => array(
		'type' => 'integer',
		'notnull' => TRUE
		),
	'peerreviewed' => array(
		'type' => 'text',
		'notnull' => TRUE
		),
	'fractweightedavg' => array(
		'type' => 'float',
		'length'=> 4,
		'notnull' => FALSE
		)
	);
/*
*index
*/
$name = 'tbl_rimfhe_chaptersinbook_idx';	

$indexes = array(
                'fields' => array(
				'chaptertitle' => array()
		)
	);
?>
