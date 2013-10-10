<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_embargos (
  id VARCHAR(32) NOT NULL,
  submissionId VARCHAR(32),
  request TINYTEXT,
  period VARCHAR(30),
  granted ENUM('yes', 'no', 'pending'),
  reason TINYTEXT,
  creatorId VARCHAR(32) NOT NULL,
  modifierId VARCHAR(32) NOT NULL,
  dateCreated DATETIME NOT NULL,
  dateModified DATETIME,
  updated TIMESTAMP(14) NOT NULL,
  PRIMARY KEY(id),
  KEY(submissionId),
  CONSTRAINT etdEmbargosFKIndex1 FOREIGN KEY (submissionId) REFERENCES tbl_etd_submissions (id)
  ON UPDATE CASCADE ON DELETE CASCADE
)TYPE=INNODB;";
*/

//5ive definition
$tablename = 'tbl_etd_embargos';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of embargoes on submitted ETDs with the period of embargo', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'submissionid' => array(
		'type' => 'text',
		'length' => 32
		),
	'request' => array(
		'type' => 'clob'
		),
	'period' => array(
		'type' => 'text',
		'length' => 30
		),
	'periodstart' => array(
		'type' => 'date'
		),
	'periodend' => array(
		'type' => 'date'
		),
	'granted' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => 1,
		'default' => 'pending'
		),
	'reason' => array(
		'type' => 'clob',
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

$name = 'etd_embargoes_id';

$indexes = array(
                'fields' => array(
                	'submissionid' => array()
                )
        );
?>