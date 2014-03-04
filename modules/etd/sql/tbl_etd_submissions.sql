<?php
/*
$sqldata[] = "CREATE TABLE tbl_etd_submissions(
    id VARCHAR(32) NOT NULL,
    authorId VARCHAR(32),
    accessLevel ENUM('private', 'public', 'protected') NOT NULL default 'private',
    status ENUM('assembly', 'pending', 'metadata', 'archived') NOT NULL default 'assembly',
    approvalLevel INT NOT NULL default 0,
    submissionType VARCHAR(50),
    commentCount INT NOT NULL default 0,
    creatorId VARCHAR(32) NOT NULL,
    modifierId VARCHAR(32) NOT NULL,
    dateCreated DATETIME NOT NULL,
    dateModified DATETIME,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY (id)
    ) type=InnoDB COMMENT='Table containing a list of uploaded files and their descriptions'";
    
*/

//5ive definition
$tablename = 'tbl_etd_submissions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing a list of submitted ETDs', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'authorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'accesslevel' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1,
		'default' => 'private'
		),
	'status' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1,
		'default' => 'assembly'
		),
	'approvallevel' => array(
		'type' => 'integer',
		'length' => 11,
		'unsigned' => 'true',
		'notnull' => 1,
		'default' => 0
		),
	'submissiontype' => array(
		'type' => 'text',
		'length' => 50
		),
	'commentcount' => array(
		'type' => 'integer',
		'length' => 11,
		'unsigned' => 'true',
		'notnull' => 1,
		'default' => 0
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

$name = 'etd_submissions_id';

$indexes = array(
                'fields' => array(
                	'authorId' => array(),
                	'status' => array(),
                	'submissionType' => array()
                )
        );
?>