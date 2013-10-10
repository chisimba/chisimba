<?php
//Table name
$tablename = 'tbl_test_shortanswer';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores short answers for a particular question', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'answers' => array(
        'type' => 'text',
        'length' => 256,
        ),
    'feedback' => array(
        'type' => 'text',
        ),
    'usecase' => array(
        'type' => 'text',
        'length' => 256,
        ),
    'timecreated' => array(
        'type' => 'timestamp'
        ),
    'createdby' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'timemodified' => array(
        'type' => 'timestamp'
        ),
    'modifiedby' => array(
        'type' => 'text',
        'length' => 32,
        )
    );
?>