<?php
/**
*Table structure for table `tbl_homepages_log`
*
*@author Alastair Pursch
*
*@package homepage
* 
*/

/*
$sqldata[]="CREATE TABLE `tbl_homepages_log` (
`id` varchar(32) NOT NULL default '',
`homepageId` varchar(32) default NULL,
`dow` int(11) default NULL,
`ip` varchar(15) default NULL,
`timestamp` datetime default NULL,
`updated` timestamp(14),
PRIMARY KEY  (`id`))
TYPE=InnoDB ROW_FORMAT=DYNAMIC";
*/

$tablename = 'tbl_homepages_log';
/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_homepages_log', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'homepageid' => array(
		'type' => 'text',
		'length' => 32
		),
	'dow' => array(
		'type' => 'integer',
		'length' => 11
		),		
	'ip' => array(
		'type' => 'text',
		'length' => 15
		),
	'timestamp' => array(
		'type' => 'timestamp'
		),	
	'updated' => array(
		'type' => 'timestamp'
		),
	);

?>
