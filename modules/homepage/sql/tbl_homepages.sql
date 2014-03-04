<?php
/**
*Table structure for table `tbl_homepages`
*
*@author Alastair Pursch
*
*@package homepage
* 
*/

/*
$sqldata[]="
CREATE TABLE `tbl_homepages` (
`id` varchar(32) NOT NULL,
`userId` varchar(25) default NULL,
`contents` text,
`updated` timestamp(14),
PRIMARY KEY  (`id`),
KEY `userId` (`userId`),
CONSTRAINT `tbl_homepages_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB ROW_FORMAT=DYNAMIC
";
*/


$tablename = 'tbl_homepages';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_homepages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'userid' => array(
		'type' => 'text',
		'length' => 25
		),	
	'contents' => array(
		'type' => 'text'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

?>