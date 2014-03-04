<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_page_questions(
    id VARCHAR(32) NOT NULL,
    pageId VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    questionId VARCHAR(32) NOT NULL,
    pageQuestionOrder INTEGER(3) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(pageId),
    KEY(surveyId),
    KEY(questionId),
    CONSTRAINT `FK_tbl_survey_page_questions_pageId` FOREIGN KEY (`pageId`) REFERENCES `tbl_survey_pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_page_questions_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_page_questions_questionId` FOREIGN KEY (`questionId`) REFERENCES `tbl_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey questions per page data'";
*/

//5ive definition
$tablename = 'tbl_survey_page_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the relationship between questions and survey pages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'page_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'question_order' => array(
        'type' => 'integer',
        'length' => 3,
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
$name = 'tbl_survey_page_questions_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'question_id' => array(),
                    'page_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>