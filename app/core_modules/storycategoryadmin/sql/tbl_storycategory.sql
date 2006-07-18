<?php
/*
$sqldata[]="CREATE TABLE `tbl_storycategory` (
  `id` varchar(32) NOT NULL,
  `category` varchar(32) NOT NULL,
  `title` varchar(250) default NULL,
  `dateCreated` datetime default NULL,
  `creatorId` varchar(32) default NULL,
  `dateModified` datetime default NULL,
  `modifierId` varchar(32) default NULL,
  `modified` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ROW_FORMAT=DYNAMIC  COMMENT='Table to hold story categories'";



$sqldata[]="INSERT INTO `tbl_storycategory` VALUES
 ('init_1','postlogin','Post login stories','2005-03-15 08:46:25','1','2005-03-15 10:05:08','1',20050315100508) ,
 ('init_2','prelogin','Prelogin public stories','2005-03-15 09:34:35','1',NULL,NULL,20050315093435) ,
 ('init_3','preloginfooter','Story for prelogin footer','2005-03-15 09:35:41','1',NULL,NULL,20050315093541) ,
 ('init_4','preloginfooter','Story for prelogin footer','2005-03-15 09:37:12','1',NULL,NULL,20050315093712)";
*/

// Table Name
$tablename = 'tbl_storycategory';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold story categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'category' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'title' => array(
		'type' => 'text',
        'length' => 250
		),
    'dateCreated' => array(
		'type' => 'date'
		),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25
		),
    'dateModified' => array(
		'type' => 'date'
		),
    'modifierId' => array(
		'type' => 'text',
        'length' => 32
		),
    'modified' => array(
		'type' => 'timestamp'
		)
    );
?>