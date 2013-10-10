<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_item(
    id VARCHAR(32) NOT NULL,
    responseId VARCHAR(32) NOT NULL,
    answerId VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    questionId VARCHAR(32) NOT NULL,
    itemName VARCHAR(25) NULL,
    itemValue TEXT NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(responseId),
    KEY(answerId),
    KEY(surveyId),
    KEY(questionId),
    CONSTRAINT `FK_tbl_survey_item_responseId` FOREIGN KEY (`responseId`) REFERENCES `tbl_survey_response` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_item_answerId` FOREIGN KEY (`answerId`) REFERENCES `tbl_survey_answer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_item_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_item_questionId` FOREIGN KEY (`questionId`) REFERENCES `tbl_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey answer items'";
*/

//5ive definition
$tablename = 'tbl_survey_item';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the answer to each survey question', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'answer_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'item_name' => array(
        'type' => 'text',
        'length' => 25,
        ),
    'item_value' => array(
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
$name = 'tbl_survey_item_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'question_id' => array(),
                    'response_id' => array(),
                    'answer_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>