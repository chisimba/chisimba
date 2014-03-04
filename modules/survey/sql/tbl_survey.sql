<?php
/*
$sqldata[]="CREATE TABLE tbl_survey(
    id VARCHAR(32) NOT NULL,
    surveyName VARCHAR(255) NULL,
    introductionLabel VARCHAR(50) NULL,
    introductionText TEXT NULL,
    thanksLabel VARCHAR(50) NULL,
    thanksText TEXT NULL,
    startDate DATETIME NULL,
    endDate DATETIME NULL,
    surveyActive SMALLINT(1) NULL,
    responseMaximum INTEGER(7) NULL,
    responseCounter INTEGER(7) NULL,
    recordedResponses SMALLINT(1) NULL,
    singleResponses SMALLINT(1) NULL,
    viewResults SMALLINT(1) NULL,
    groupEmailSent SMALLINT(1) NULL,
    commentCount INT(4) NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(creatorId),
    CONSTRAINT `FK_tbl_survey_creatorId` FOREIGN KEY (`creatorId`) REFERENCES `tbl_users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=InnoDB COMMENT='Survey Manager'";
*/

//5ive definition
$tablename = 'tbl_survey';

//Options line for comments, encoding and character set
$options = array('comment' => 'The main survey database table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'survey_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'intro_label' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'intro_text' => array(
        'type' => 'clob',
        ),
    'thanks_label' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'thanks_text' => array(
        'type' => 'clob',
        ),
    'start_date' => array(
        'type' => 'timestamp',
        ),
    'end_date' => array(
        'type' => 'timestamp',
        ),
    'survey_active' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'max_responses' => array(
        'type' => 'integer',
        'length' => 7,
        ),
    'response_counter' => array(
        'type' => 'integer',
        'length' => 7,
        ),
    'recorded_responses' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'single_responses' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'view_results' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'email_sent' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'commentcount' => array(
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
    'login' => array(
        'type' => 'text',
        'length' => 1,
        )
    );

// create other indexes here...
$name = 'tbl_survey_index';

$indexes = array(
                'fields' => array(
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>