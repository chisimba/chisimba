<?php
/**
*Table structure for table `tbl_glossary_urls`
*
*@author Alastair Pursch
*
*@package glossary
* 
*/

/*
$sqldata[]="CREATE TABLE `tbl_glossary_urls` ("
."  `id` VARCHAR(32) NOT NULL,"
."  `item_id` VARCHAR(32) NOT NULL default '0',"
."  `url` varchar(100) NOT NULL default '',"
."  `userId` varchar(50) NOT NULL default '',"
."  `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',"
."  PRIMARY KEY  (`id`),"
."  INDEX `item_id` (`item_id`),"
."  FOREIGN KEY (`item_id`) REFERENCES `tbl_glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
.") TYPE=InnoDB;";
*/

$tablename = 'tbl_glossary_urls';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_glossary_urls', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'item_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'url' => array(
		'type' => 'text',
		'length' => 100,
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
