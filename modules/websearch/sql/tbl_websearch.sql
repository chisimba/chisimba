<?php
/*$sqldata[]="CREATE TABLE `tbl_websearch` (
  `id` varchar(32) NOT NULL default '',
  `userId` varchar(25) NOT NULL default '',
  `searchterm` varchar(255) default NULL,
  `module` varchar(32) default NULL,
  `context` varchar(50) default 'lobby',
  `params` varchar(32) default NULL,
  `dateCreated` datetime default NULL,
  `searchengine` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`),
  FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB ROW_FORMAT=DYNAMIC  COMMENT='Table to hold the results of intercepted searches'";*/


$tablename = 'tbl_websearch';

$options = array('comment' => 'This table stores temporary uploads while an event is being created', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25
		),
	'searchterm' => array(
		'type' => 'text',
		'length' => 255
		),
	'module' => array(
		'type' => 'text',
		'length' => 32
		),
	'context' => array(
		'type' => 'text',
		'length' => 50
		),
	'params' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'date',
		
		),
	'searchengine' => array(
		'type' => 'text',
		'length' => 50
		)
	);


?>
