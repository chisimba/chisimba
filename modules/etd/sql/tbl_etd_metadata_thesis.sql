<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_metadata_thesis (
    id VARCHAR(32) NOT NULL,
    dcMetaId VARCHAR(32),
    submitId VARCHAR(32),
    thesis_degree_name VARCHAR(255),
    thesis_degree_level VARCHAR(255),
    thesis_degree_discipline VARCHAR(255),
    thesis_degree_grantor VARCHAR(255),
    dateCreated DATETIME,
    creatorId VARCHAR(32),
    dateModified DATETIME,
    modifierId VARCHAR(32),
    PRIMARY KEY (id),
    INDEX dcMetaId (dcMetaId),
    KEY submitId (submitId),
    CONSTRAINT etdthesisSubmitId FOREIGN KEY (submitId) REFERENCES tbl_etd_submissions (id)
    ON DELETE CASCADE ON UPDATE CASCADE
    )TYPE=InnoDB COMMENT='metadata fields for the extended dublin core for theses';";
*/

    //5ive definition
$tablename = 'tbl_etd_metadata_thesis';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the dublin core metadata extended fields for theses', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'dcmetaid' => array(
		'type' => 'text',
		'length' => 32
		),
	'submitid' => array(
		'type' => 'text',
		'length' => 32
		),
	'thesis_degree_name' => array(
		'type' => 'text',
		'length' => 255
		),
	'thesis_degree_level' => array(
		'type' => 'text',
		'length' => 255
		),
	'thesis_degree_discipline' => array(
		'type' => 'text',
		'length' => 255
		),
	'thesis_degree_faculty' => array(
		'type' => 'text',
		'length' => 255
		),
	'thesis_degree_grantor' => array(
		'type' => 'text',
		'length' => 255
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

$name = 'etd_thesis_id';

$indexes = array(
                'fields' => array(
                	'dcmetaid' => array(),
                	'submitid' => array()
                )
        );
?>