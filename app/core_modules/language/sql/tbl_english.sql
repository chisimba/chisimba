<?php
/*
CREATE TABLE `tbl_english` (
  `code` varchar(50) NOT NULL default '',
  `Content` mediumtext,
  `isInNextGen` tinyint(1) default NULL,
  `dateCreated` datetime default '2004-06-23 19:46:00',
  `creatorUserId` varchar(25) default '1',
  `dateLastModified` datetime default NULL,
  `modifiedByUserId` varchar(25) default NULL,
  PRIMARY KEY  (code)
) TYPE=InnoDB ;
*/
// Table Name
$tablename = 'tbl_en';

//Options line for comments, encoding and character set
$options = array('comment' => 'English language table','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 50,

		),
    'en' => array(
		'type' => 'text',
		'length' => 255,
		),
	'pageId' => array(
		'type' => 'text',
		'length' => 150
		),
    'isInNextGen' => array(
		'type' => 'integer',
		'length' => 1
		),
    'dateCreated' => array(
		'type' => 'date'
		),
    'creatorUserId' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => TRUE
        'default' => '1'
		),
    'dateLastModified' => array(
		'type' => 'date'
		),
    'modifiedByUserId' => array(
		'type' => 'text',
		'length' => 25
		)
    );

//create other indexes here...

$name = 'eng_code';

$indexes = array(
                'fields' => array(
                	'id' => array()
                )
        );
?>
