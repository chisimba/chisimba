<?php

/*
*Table to hold book  details
*/

/*
*Set table name
*/
$tablename = 'tbl_rimfhe_books';

/*
*options for comment
*/
	
$options = array(
	'comment' => 'This table stores data of books in tbl_rimfhe_books',
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
	'publishinghouse' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'authorname' => array(
		'type' => 'text',
		'length'=> 100,
		'notnull' => TRUE
		),
	'firstchapterpageno' => array(
		'type' => 'integer',
		'notnull' => TRUE
		),
	'lastchapterpageno' => array(
		'type' => 'integer',
		'notnull' => TRUE
		),
	'totalpages' => array(
		'type' => 'integer',
		'length'=> 100,
		'notnull' => TRUE
		),
	'peerreviewed' => array(
		'type' => 'text',
		'notnull' => TRUE
		)
	);
/*
*index
*/
$name = 'tbl_rimfhe_books_idx';	

$indexes = array(
                'fields' => array(
				'isbn' => array()
		)
	);
?>
