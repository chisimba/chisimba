<?php
//Table definition
$tablename = 'tbl_test_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores a list of questions for a test', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'testid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'categoryid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'name' => array(
        'type'=>'text',
        'length'=>32
        ),
    'question' => array(
        'type' => 'clob',
        ),
    'hint' => array(
        'type' => 'text',
        'length' => 120,
        ),
    'mark' => array(
        'type' => 'integer',
        'length' => 5,
        ),
    'questionorder' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'questiontype' => array(
        'type' => 'text',
        'length' => 25,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    'questiontext' => array(
        'type'=>'text',
        'length'=>255
        ),
    'generalfeedback' => array(
        'type'=>'text',
        'length'=>255
        ),
    'penalty' => array(
        'type'=>'text',
        'length'=>12
        ),
    'qtype'  => array(
        'type'=>'text',
        'length'=>20
        ),
    'length' => array(
        'type'=>'text',
        'length'=>10
        ),
    'stamp'  => array(
        'type'=>'text',
        'length'=>255
        ),
    'version' => array(
        'type'=>'text',
        'length'=>255
        ),
    'hidden'  => array(
        'type'=>'text',
        'length'=>1
        ),
    'timecreated'  => array(
        'type'=>'timestamp'
        ),
    'timemodified' => array(
        'type'=>'timestamp'
        ),
    'createdby' => array(
        'type'=>integer,
        'length' =>20
        ),
    'modifiedby'=> array(
        'type'=>integer,
        'length' =>20
        )
);

// create other indexes here...
$name = 'test_questions_index';

$indexes = array(
                'fields' => array(
                'testid' => array(),
                'categoryid' => array()
            )
        );
?>