<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_config (
    id VARCHAR(32) NOT NULL,
    grantEmbargo ENUM('yes', 'no') NOT NULL default 'no',
    requestEmbargo ENUM('yes', 'no') NOT NULL default 'yes',
    finalApproval ENUM('yes', 'no') NOT NULL default 'yes',
    finalApprovalBypass ENUM('yes', 'no') NOT NULL default 'no',
    grantEmbargoL2 ENUM('yes', 'no') NOT NULL default 'yes',
    shortPeriod INT,
    longPeriod INT,
    incPeriod INT,    
    creatorId VARCHAR(32) NOT NULL,
    modifierId VARCHAR(32) NOT NULL,
    dateCreated DATETIME NOT NULL,
    dateModified DATETIME,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id)
)TYPE=INNODB;";
*/

//5ive definition
$tablename = 'tbl_etd_config';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the configuration settings for the system', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'grantEmbargo' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => 1,
		'default' => 'no'
		),
	'requestEmbargo' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => 1,
		'default' => 'yes'
		),
	'finalApproval' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => 1,
		'default' => 'yes'
		),
	'finalApprovalBypass' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => 1,
		'default' => 'no'
		),
	'grantEmbargoL2' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => 1,
		'default' => 'yes'
		),
	'shortPeriod' => array(
		'type' => 'integer',
		'length' => 11
		),
	'longPeriod' => array(
		'type' => 'integer',
		'length' => 11
		),
	'incPeriod' => array(
		'type' => 'integer',
		'length' => 11
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'datemodified' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

?>