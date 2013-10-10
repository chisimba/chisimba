<?php
/*
$sqldata[]="CREATE TABLE `tbl_readinglist` (
`id` VARCHAR( 32 ) NOT NULL ,
`contextCode` VARCHAR( 255 ),
`author` VARCHAR(50 ) NOT NULL ,
`title` VARCHAR( 100 ) NOT NULL ,
`publisher` VARCHAR( 50 ) NOT NULL ,
`publishingYear` VARCHAR( 4 ) NOT NULL ,
`link` VARCHAR( 150 ) NOT NULL,
`publication` TEXT  NOT NULL,
PRIMARY KEY ( `id` ) 
)
TYPE=InnoDB";
*/

$tablename = 'tbl_readinglist';

/*
Options line for comments, encoding and character set
*/
$options = array('comment' => '', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

/*Fields
*/
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255
		),	
	'author' => array(
		'type' => 'text',
		'length' => 50
		),
	'title' => array(
		'type' => 'text',
		'length' => 100
		),
	'publisher' => array(
		'type' => 'text',
		'length' => 50
		),
	'publishingyear' => array(
		'type' => 'text',
		'length' => 4
		),
	'link' => array(
		'type' => 'text',
		'length' => 50
		),
	'publication' => array(
		'type' => 'text'
		),
	'country' => array(
		'type' => 'text',
		'length' => 50
		),
	'language' => array(
		'type' => 'text',
		'length' => 50
		),
	);
	


?>