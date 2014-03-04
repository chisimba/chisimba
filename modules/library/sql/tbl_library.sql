<?php
/*
$sqldata[]="CREATE TABLE `tbl_library` (                                                                                                                                                                                          
              `id` varchar(32) NOT NULL default '',                                                                                                                                                                                                                                                                                                           
              `title` text,                                                                                                                                                                                                                                                                                                                                   
              `description` text,                                                                                                                                                                                                                                                                                                                             
              `url` varchar(255) default NULL,                                                                                                                                                                                                                                                                                                                
              `creatorId` varchar(25) default NULL,                                                                                                                                                                                                                                                                                                           
              `dateCreated` datetime default NULL,                                                                                                                                                                                                                                                                                                            
              `modifierId` varchar(25) default NULL,                                                                                                                                                                                                                                                                                                          
              `dateModified` datetime default NULL,                                                                                                                                                                                     
              PRIMARY KEY  (`id`)                                                                                                                                                                                                  
) TYPE=InnoDB ROW_FORMAT=DYNAMIC  COMMENT='Table to hold a list of library links'";
*/

$tablename = 'tbl_library';

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
	'title' => array(
		'type' => 'text'
		),	
	'description' => array(
		'type' => 'text'
		),
	'url' => array(
		'type' => 'text',
		'length' => 255
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 25
		),
	'datecreated' => array(
		'type' => 'date'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 25
		),
	'datemodified' => array(
		'type' => 'date'
		),
	);
?>
