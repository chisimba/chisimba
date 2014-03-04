<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_comment(
    id VARCHAR(32) NOT NULL,
    responseId VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    questionId VARCHAR(32) NOT NULL,
    comments TEXT NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(responseId),
    KEY(surveyId),
    KEY(questionId),
    CONSTRAINT `FK_tbl_survey_comment_responseId` FOREIGN KEY (`responseId`) REFERENCES `tbl_survey_response` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_comment_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_comment_questionId` FOREIGN KEY (`questionId`) REFERENCES `tbl_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey question comments'";
*/

//5ive definition
$tablename = 'tbl_survey_comment';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table that holds the comments on the questions by the respondents', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'survey_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'question_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'response_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'question_comment' => array(
        'type' => 'clob',
        ),
    'creator_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'date_created' => array(
        'type' => 'timestamp',
        ),
    'modifier_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'date_modified' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'tbl_survey_comment_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'question_id' => array(),
                    'response_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>