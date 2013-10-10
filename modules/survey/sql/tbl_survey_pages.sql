<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_pages(
    id VARCHAR(32) NOT NULL,
    surveyId VARCHAR(32) NOT NULL,
    pageOrder INTEGER(3) NULL,
    pageLabel VARCHAR(50) NULL,
    pageText TEXT NULL,
    creatorId VARCHAR(25) NOT NULL,
    dateCreated DATETIME NOT NULL,
    modifierId VARCHAR(25) NULL,
    dateModified DATETIME NULL,
    updated TIMESTAMP(14) NOT NULL,
    PRIMARY KEY(id),
    KEY(surveyId),
    CONSTRAINT `FK_tbl_survey_page_surveyId` FOREIGN KEY (`surveyId`) REFERENCES `tbl_survey` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) TYPE=INNODB COMMENT='Survey page data'";
*/

//5ive definition
$tablename = 'tbl_survey_pages';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding survey page information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'survey_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'page_order' => array(
        'type' => 'integer',
        'length' => 3,
        ),
    'page_label' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'page_text' => array(
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
$name = 'tbl_survey_pages_index';

$indexes = array(
                'fields' => array(
                    'survey_id' => array(),
                    'creator_id' => array(),
                    'modifier_id' => array(),
                )
        );
?>