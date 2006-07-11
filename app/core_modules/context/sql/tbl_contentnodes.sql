<?
/*
  $sqldata[]="CREATE TABLE `tbl_contentnodes` (
  `id` varchar(32) NOT NULL,
  `parentNodeId` varchar(32) default NULL,
  `prevNodeId` varchar(32) default NULL,
  `nextNodeId` varchar(32) default NULL,
  `userId` varchar(20) default NULL,
  `body` varchar(200) default NULL,
  `datecreated` datetime default NULL,
  `datemodified` datetime default NULL,
  `menutext` varchar(50) default NULL,
  `title` TEXT default NULL,
  `script` TEXT default NULL,  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ";

$sqldata[]="INSERT INTO `tbl_contentnodes` VALUES ('1kng', NULL, NULL, NULL, '1', 'Replace this body text with your own text for Enter Page.', '2004-08-05 10:16:33', '2004-08-05 10:16:33', 'Replace me(root)', 'Add  your page Title here')";
$sqldata[]="INSERT INTO `tbl_contentnodes` VALUES ('2kng', '1kng', '1kng', NULL, '1', 'Replace this body text with your own text for page 1. This is just here as placeholder to make it easy for you to edit and add pages to your course. Use the edit and insert buttons to edit and insert', '2004-08-05 10:16:33', '2004-08-05 10:16:33', 'First Page', 'Add  your page Title here')";
*/

$tablename = 'tbl_contentnodes';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parentNodeId' => array(
		'type' => 'text',
		'length' => 64
		),
	'prevNodeId' => array(
		'type' => 'text',
		'length' => 60
		),
    'nextNodeId' => array(
		'type' => 'text',
		'length' => 32
		),
	'userId' => array(
		'type' => 'text',
		'length' => 25
		),
	'body' => array(
		'type' => 'text',
		'length' => 200
		),
    'datecreated' => array(
		'type' => 'datetime'
		),
	'datemodified' => array(
		'type' => 'datetime'
		),
	'menutext' => array(
		'type' => 'text',
		'length' => 50
		),
    'title' => array(
		'type' => 'text'
		),
	'script' => array(
		'type' => 'text'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
    );
?>