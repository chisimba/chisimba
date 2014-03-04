<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_question_rows(
    id VARCHAR(32) NOT NULL,
    questionId VARCHAR(32) NOT NULL,
    rowOrder INTEGER(3) NULL,
    rowText TEXT NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(questionId),
    CONSTRAINT `FK_tbl_survey_question_rows_questionId` FOREIGN KEY (`questionId`) REFERENCES `tbl_survey_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey question row data'";
*/

//5ive definition
$tablename = 'tbl_survey_question_rows';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the question row data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'row_order' => array(
        'type' => 'integer',
        'length' => 3,
        ),
    'row_text' => array(
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
$name = 'tbl_survey_question_rows_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'question_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>