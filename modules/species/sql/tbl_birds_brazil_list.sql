<?php
/**
*
* Table for birds of brazil
*
*/
// Table Name
$tablename = 'tbl_birds_brazil_list';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of species primary data for birds in the species module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'alphabeticalname' => array(
		'type' => 'text',
		'length' => 250,
		),
	'fullname' => array(
		'type' => 'text',
		'length' => 250,
		),
	'scientificname' => array(
		'type' => 'text',
		'length' => 250,
		),
	);

//create other indexes here...

$name = 'tbl_birds_brazil_list_idx';

$indexes = array(
    'fields' => array(
         'alphabeticalname' => array(),
         'fullname' => array(),
         'scientificname' => array(),
    )
);
?>