<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_response(
    id VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    userId VARCHAR(25) NOT NULL,
    respondentNumber INTEGER(3) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(surveyId),
    KEY(userId),
    CONSTRAINT `FK_tbl_survey_response_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_tbl_survey_response` FOREIGN KEY (`userId`) REFERENCES `tbl_users` (`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey responses'";
*/

//5ive definition
$tablename = 'tbl_survey_response';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the respondent information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'survey_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'respondent_number' => array(
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
$name = 'tbl_survey_response_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'user_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>