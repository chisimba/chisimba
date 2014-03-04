<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_collection_submission(
    id VARCHAR(32) NOT NULL,
    collectionId VARCHAR(32),
    submissionId VARCHAR(32),
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id),
    KEY collectionId (collectionId),
    KEY submissionId (submissionId),
    CONSTRAINT etdBridgeCollectId FOREIGN KEY (collectionId) REFERENCES tbl_etd_collections (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT etdBridgeSubmitId FOREIGN KEY (submissionId) REFERENCES tbl_etd_submissions (id)
    ON DELETE CASCADE ON UPDATE CASCADE
    ) type=InnoDB COMMENT='Bridge table between collections and submissions'";
*/

//5ive definition
$tablename = 'tbl_etd_collection_submission';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table between collections and submissions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'collectionid' => array(
		'type' => 'text',
		'length' => 32
		),
	'submissionid' => array(
		'type' => 'text',
		'length' => 32
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
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'etd_collection_bridge_id';

$indexes = array(
                'fields' => array(
                	'collectionid' => array(),
                	'submissionid' => array()
                )
        );
?>