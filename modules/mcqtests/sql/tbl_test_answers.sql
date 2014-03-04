<?php
/*
$sqldata[]="CREATE TABLE tbl_test_answers (
    id varchar(32) PRIMARY KEY NOT NULL,
    questionId varchar(32) NOT NULL,
    answer text,
    commentText varchar(120),
    answerOrder int default 0,
    correct tinyint default 0,
    `updated` TIMESTAMP(14) NOT NULL,
    KEY `questionId` (`questionId`),
    CONSTRAINT `testAnswersQuestions` FOREIGN KEY (`questionId`) REFERENCES `tbl_test_questions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE) type=InnoDB
    COMMENT='This table lists the answers and comments for the questions in a test'";
*/

//5ive definition
$tablename = 'tbl_test_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table lists the answers and comments for the questions in a test', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'answer' => array(
        'type' => 'clob',
        ),
    'commenttext' => array(
        'type' => 'text',
        'length' => 120,
        ),
    'answerorder' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'correct' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'test_answers_index';

$indexes = array(
                'fields' => array(
                    'testid' => array(),
                    'questionid' => array(),
                ),
        );
?>
