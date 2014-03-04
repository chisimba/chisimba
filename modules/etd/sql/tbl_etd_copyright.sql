<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_copyright (
    id VARCHAR(32) NOT NULL,
    language CHAR(2),
    copyright TEXT,
    creatorId VARCHAR(32) NOT NULL,
    modifierId VARCHAR(32) NOT NULL,
    dateCreated DATETIME NOT NULL,
    dateModified DATETIME,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id)
)TYPE=INNODB;";
*/


//5ive definition
$tablename = 'tbl_etd_copyright';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the copyright to be accepted by a student on submitting an ETD', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'language' => array(
		'type' => 'text',
		'length' => 2
		),
	'copyright' => array(
		'type' => 'clob'
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