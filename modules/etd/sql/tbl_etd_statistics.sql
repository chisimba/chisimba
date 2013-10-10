<?php
/*
$sqldata[] = "CREATE TABLE tbl_cshe_statistics (
  id VARCHAR(32) NOT NULL,
  submitId VARCHAR(32),
  hitType ENUM('visit', 'hit', 'download', 'upload'),
  ipAddress VARCHAR(32),
  countryCode CHAR(4),
  creatorId VARCHAR(32),
  dateCreated DATETIME,
  updated TIMESTAMP(14) NOT NULL,
  PRIMARY KEY(id)
  )TYPE=InnoDB COMMENT='Table containing the statistics for the hits to the site and, views and downloads on resources';";
*/


//5ive definition
$tablename = 'tbl_etd_statistics';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the statistics for the hits to the site and, views and downloads on resources', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'submitid' => array(
		'type' => 'text',
		'length' => 32
		),
	'hittype' => array(
		'type' => 'text',
		'length' => 15,
		'notnull' => 1,
		'default' => 'hit'
		),
	'ipaddress' => array(
		'type' => 'text',
		'length' => 32
		),
	'countrycode' => array(
		'type' => 'text',
		'length' => 4
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here... 

$name = 'etd_statistics_index';

$indexes = array(
                'fields' => array(
                	'hittype' => array(),
                	'submitid' => array(),
                	'countrycode' => array()
                )
        );   
?>