<?php
/*
$sqldata[]="CREATE TABLE tbl_test_marked (
    id varchar(32) PRIMARY KEY NOT NULL,
    testId varchar(32) NOT NULL,
    questionId varchar(32) NOT NULL,
    answerId varchar(32),
    studentId VARCHAR(32) NOT NULL,
    `updated` TIMESTAMP(14) NOT NULL,
    KEY `questionId` (`questionId`),
    KEY `studentId` (`studentId`),
    CONSTRAINT `testMarkedQuestion` FOREIGN KEY (`questionId`) REFERENCES `tbl_test_questions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `testMarkedStudent` FOREIGN KEY (`studentId`) REFERENCES `tbl_users` (`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE) type=InnoDB
    COMMENT='This table lists the answer given by a student for an mcq question'";
*/

//5ive definition
$tablename = 'tbl_test_marked';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table lists the answer given by a student for an mcq question', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'testid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'answerid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'studentid' => array(
        'type' => 'text',
        'length' => 32,
        ),
     'answered' => array(
        'type' => 'text',
        'length' => 120,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'test_marked_index';

$indexes = array(
                'fields' => array(
                    'testid' => array(),
                    'questionid' => array(),
                    'answerid' => array(),
                    'studentid' => array(),
                ),
        );
?>