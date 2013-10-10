<?php
/*
$sqldata[]="CREATE TABLE tbl_survey_question_type(
    id VARCHAR(32) NOT NULL,
    questionDescription VARCHAR(255) NULL,
    questionType SMALLINT(2) NULL,
    PRIMARY KEY(id)
    ) TYPE=InnoDB COMMENT='List of question types'";

$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Choice - Multiple answers - Checkboxes','1')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Choice - One answer (Options or Dropdown)','2')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Matrix - Multiple answers per row - Checkboxes','3')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Matrix - Multiple answers per row - Text','4')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Matrix - One answer per row - Options','5')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Matrix - Numerical rating scale - Options','6')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Open ended - Text','7')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Open ended - Constant sum','8')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Open ended - Number','9')";
$sqldata[]="INSERT INTO tbl_survey_question_type(id,questionDescription,questionType)
    values('PKVALUE','Open ended - Date','10')";
*/

//5ive definition
$tablename = 'tbl_survey_question_type';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table holding the survey question types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'question_description' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'question_type' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    );

/*
// create other indexes here...
$name = 'tbl_survey_question_type_index';

$indexes = array(
                'fields' => array()
        );
*/
?>