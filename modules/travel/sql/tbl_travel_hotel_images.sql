<?php
/*
Set the table name
*/
$tablename = 'tbl_travel_hotel_images';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for hotel image urls', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 10,
		'notnull' => 1
		),
	'name' => array(
		'type' => 'text',
		'length' => 64
		),
    'caption' => array(
		'type' => 'text',
		'length' => 64
		),
    'url' => array(
		'type' => 'text',
		'length' => 128
		), 
	'supplier' => array(
		'type' => 'text',
		'length' => 5
		),
	'width' => array(
		'type' => 'integer',
		'length' => 4
		),
	'height' => array(
		'type' => 'integer',
		'length' => 4
		),
	'bytesize' => array(
		'type' => 'integer',
		'length' => 4
		),
	'thumbnail' => array(
		'type' => 'text',
		'length' => 128
		),
	
	);
	
$name = "tbl_hotelimages_idx";

?>