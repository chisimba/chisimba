<?php
/*
$sqldata[]="CREATE TABLE tbl_test_results (
    id varchar(32) PRIMARY KEY NOT NULL,
    testId VARCHAR(32) NOT NULL,
    studentId VARCHAR(32) NOT NULL,
    mark int NOT NULL DEFAULT 0,
    startTime DATETIME NULL,
    endTime DATETIME NULL,
    `updated` TIMESTAMP(14) NOT NULL,
    KEY `studentId` (`studentId`),
    CONSTRAINT `testResultsStudent` FOREIGN KEY (`studentId`) REFERENCES `tbl_users` (`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE) type=InnoDB
    COMMENT='This table lists the mark given to a student on a test.'";
*/

//5ive definition
$tablename = 'tbl_test_results';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table lists the mark given to a student on a test.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'testid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'studentid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'mark' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'starttime' => array(
        'type' => 'timestamp',
        ),
    'endtime' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'test_results_index';

$indexes = array(
                'fields' => array(
                    'testid' => array(),
                    'studentid' => array(),
                ),
        );
?>