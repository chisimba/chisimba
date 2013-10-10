<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_question(
    id VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    typeId VARCHAR(32) NOT NULL,
    questionOrder INTEGER(3) NULL,
    questionText TEXT NULL,
    questionSubtext TEXT NULL,
    compulsoryQuestion SMALLINT(1) NULL,
    verticalAlignment SMALLINT(1) NULL,
    commentRequested SMALLINT(1) NULL,
    commentText TEXT NULL,
    htmlElementType SMALLINT(1) NULL,
    booleanType SMALLINT(1) NULL,
    trueOrFalse SMALLINT(1) NULL,
    ratingScale SMALLINT(2) NULL,
    constantSum INTEGER(5) NULL,
    minimumNumber INTEGER(7) NULL,
    maximumNumber INTEGER(7) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(surveyId),
    KEY(typeId),
    CONSTRAINT `FK_tbl_survey_question_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_question_typeId` FOREIGN KEY (`typeId`) REFERENCES `tbl_survey_question_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey question data'";
*/

//5ive definition
$tablename = 'tbl_survey_question';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the survey questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'survey_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'type_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'question_order' => array(
        'type' => 'integer',
        'length' => 3,
        ),
    'question_text' => array(
        'type' => 'clob',
        ),
    'question_subtext' => array(
        'type' => 'clob',
        ),
    'compulsory_question' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'vertical_alignment' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'comment_requested' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'comment_request_text' => array(
        'type' => 'clob',
        ),
    'radio_element' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'preset_options' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'true_or_false' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'rating_scale' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'constant_sum' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'minimum_number' => array(
        'type' => 'integer',
        'length' => 7,
        ),
    'maximum_number' => array(
        'type' => 'integer',
        'length' => 7,
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
$name = 'tbl_survey_question_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'type_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>