<?php
/*
CREATE TABLE `tbl_modcat` (
  `id` varchar(32) UNIQUE,
  `modName` varchar(50) NOT NULL DEFAULT '',
  `description` mediumtext,
  `category` varchar(50) NOT NULL DEFAULT 'Other',
  `dateCreated` datetime DEFAULT '2006-06-06 06:06:06',
  `creatorUserId` varchar(25) DEFAULT '1',
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedByUserId` varchar(25) NOT NULL DEFAULT 1,
  PRIMARY KEY  (id)
) TYPE=InnoDB ;

#
# Dumping data for table `tbl_modcat`
#
INSERT INTO tbl_modcat (id,modName,description,category) VALUES ('init_1','modulecatalogue','Categorical module manager and updater','Admin');
*/
// Table Name
$tablename = 'tbl_modcat';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'modName' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => ''
		),
    'description' => array(
		'type' => 'text'
		),
    'category' => array(
		'type' => 'text',
        'length' => 50,
        'notnull' => TRUE,
        'default' => 'Other'
		),
    'dateCreated' => array(
		'type' => 'datetime',
        'default' => '2006-06-06 06:06:06'
		),
    'creatorUserId' => array(
		'type' => 'text',
		'length' => 25,
        'default' => 1
		),
    'dateLastModified' => array(
		'type' => 'datetime'
		),
    'modifiedByUserId' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        'default' => '1'
		)
    );

?>