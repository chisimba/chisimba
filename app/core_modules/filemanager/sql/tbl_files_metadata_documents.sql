<?php
/*
CREATE TABLE `tbl_files_metadata_documents` (
  `id` varchar(32) NOT NULL,
  `fileId` varchar(32) NOT NULL,
  `title` varchar(255) default NULL,
  `author` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `keywords` text,
  `documentDate` datetime default NULL,
  `statWords` int(11) default '0',
  `statChars` int(11) default '0',
  `creatorId` varchar(25) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `modifierId` varchar(25) NOT NULL,
  `dateModified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fileId` (`fileId`)
) TYPE=MyISAM COMMENT='Table to hold metadata for documents';
;
*/
$tablename = 'tbl_files_metadata_documents';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'title' => array(
		'type' => 'text',
        'length' => 255
		),
    'author' => array(
		'type' => 'text',
        'length' => 255
		),
    'subject' => array(
		'type' => 'text',
        'length' => 255
		),
    'keywords' => array(
		'type' => 'text'
		),
    'documentdate' => array(
		'type' => 'date'
		),
    'statwords' => array(
		'type' => 'integer',
        'length' => 11
		),
    'statchars' => array(
		'type' => 'integer',
        'length' => 11
		),
    'creatorid' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'datecreated' => array(
		'type' => 'date'
		),
    'timecreated' => array(
		'type' => 'time'
		),
    'modifierid' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'datemodified' => array(
		'type' => 'date'
		),
    'timemodified' => array(
		'type' => 'time'
		)
    );
//create other indexes here...

$name = 'index_tbl_files_metadata_documents';

$indexes = array(
                'fields' => array(
                	'fileid' => array()
                )
        );

?>