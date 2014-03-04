<?php
/**
*Table structure for table `bridge_glossary_seealso`
*
*@author Alastair Pursch
*
*@package glossary
* 
*/

/*
$sqldata[]="CREATE TABLE `bridge_glossary_seealso` ("
."  `id` VARCHAR(32) NOT NULL ,"
."  `item_id` VARCHAR(32) NOT NULL default '0',"
."  `item_id2` VARCHAR(32) NOT NULL default '0',"
."  `userId` varchar(50) NOT NULL default '',"
."  `dateLastUpdated` datetime NOT NULL default '0000-00-00 00:00:00',"
."  PRIMARY KEY  (`id`),"
."  INDEX `item_id` (`item_id`,`item_id2`),"
."  INDEX `item_id2` (`item_id2`),"
."  FOREIGN KEY (`item_id`) REFERENCES `tbl_glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,"
."  FOREIGN KEY (`item_id2`) REFERENCES `tbl_glossary` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
.") TYPE=InnoDB;";
*/

$tablename = 'bridge_glossary_seealso';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for bridge_glossary_seealso', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'item_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'item_id2' => array(
		'type' => 'text',
		'length' => 32
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