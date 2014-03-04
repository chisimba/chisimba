<?php
/*
$sqldata[]="CREATE TABLE tbl_tests (
    id varchar(32) PRIMARY KEY NOT NULL,
    context varchar(255),
    chapter varchar(32),
    userId varchar(32),
    name varchar(60),
    description text,
    status enum('inactive','open') NOT NULL default 'inactive',
    totalMark INT NOT NULL default 0,
    percentage FLOAT,
    duration INT NOT NULL DEFAULT 0,
    timed TINYINT NOT NULL DEFAULT 0,
    testType VARCHAR(20) NOT NULL DEFAULT 'Formative','summative'
    qSequence VARCHAR(20) NOT NULL DEFAULT 'Sequential','scrambled''
    aSequence VARCHAR(20) NOT NULL DEFAULT 'Sequential',
    comLab VARCHAR(50) NULL,
    startDate datetime,
    closing_date datetime,
    last_modified datetime,
    `updated` TIMESTAMP(14) NOT NULL,
    KEY `userId` (`userId`),
    CONSTRAINT `testStudent` FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE
    ) type=InnoDB
    COMMENT='This table stores a list of tests and their contexts'";
*/

//5ive definition
$tablename = 'tbl_tests';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores a list of tests and their contexts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'context' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'chapter' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'name' => array(
        'type' => 'text',
        'length' => 60,
        ),
    'description' => array(
        'type' => 'clob',
        ),
    'status' => array(
        'type' => 'text',
        'length' => 8,
        ),
    'totalmark' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'percentage' => array(
        'type' => 'float',
        ),
    'duration' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'timed' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'testtype' => array(
        'type' => 'text',
        'length' => 9,
        ),
    'qsequence' => array(
        'type' => 'text',
        'length' => 10,
        ),
    'asequence' => array(
        'type' => 'text',
        'length' => 10,
        ),
    'comlab' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'startdate' => array(
        'type' => 'timestamp',
        ),
    'closingdate' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    'coursePermissions' => array(
        'type' => 'text',
        'length' => 15
        )
    );

// create other indexes here...
$name = 'test_index';

$indexes = array(
                'fields' => array(
                    'context' => array(),
                    'chapter' => array(),
                    'userid' => array(),
                ),
        );
?>