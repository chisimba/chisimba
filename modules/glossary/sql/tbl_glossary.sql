<?php
/**
*Table structure for table `tbl_glossary`
*
*@author Alastair Pursch
*
*@package glossary
* 
*/

/*
$sqldata[]="CREATE TABLE `tbl_glossary` ("
."  `id` VARCHAR(32) NOT NULL ,"
."  `context` varchar(50) NOT NULL default '',"
."  `term` varchar(100) NOT NULL default '',"
."  `definition` text NOT NULL,"
."  `userId` varchar(50) NOT NULL default '',"
."  `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',"
."  PRIMARY KEY  (`id`),"
."  INDEX `item_id2` (`id`)"
.") TYPE=InnoDB;";
*/

$tablename = 'tbl_glossary';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_glossary', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'context' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => 1
		),
	'term' => array(
		'type' => 'text',
		'length' => 100,
		'notnull' => 1
		),		
	'definition' => array(
		'type' => 'text',
		'notnull' => 1
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => 1		
		),	
	'datelastupdated' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	);
?>