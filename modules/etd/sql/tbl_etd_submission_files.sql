<?php

/*
$sqldata[] = "CREATE TABLE tbl_etd_submission_files(
    id VARCHAR(32) NOT NULL,
    submissionId VARCHAR(32) NOT NULL,
    description TINYTEXT,
    fileName VARCHAR(120),
    fileId VARCHAR(100),
    creatorId VARCHAR(32) NOT NULL,
    modifierId VARCHAR(32),
    dateCreated DATETIME NOT NULL,
    dateModified DATETIME,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id),
    KEY submissionId (submissionId),
    CONSTRAINT etd_files_submitId FOREIGN KEY (submissionId) REFERENCES tbl_etd_submissions (id)
    ON DELETE CASCADE ON UPDATE CASCADE
    ) type=InnoDB COMMENT='Table containing a list of uploaded files and their descriptions'";
*/

//5ive definition
$tablename = 'tbl_etd_submission_files';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of uploaded files and their descriptions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'submissionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'filename' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'storedname' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'mimetype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'filesize' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'datecreated' => array(
        'type' => 'timestamp'
        ),
    'updated' => array(
        'type' => 'timestamp'
        ),
    );

// create other indexes here...

$name = 'etd_files_id';

$indexes = array(
                'fields' => array(
                    'submissionid' => array(),
                )
        );
?>