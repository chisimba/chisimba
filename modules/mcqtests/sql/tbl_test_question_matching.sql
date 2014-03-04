<?php
//Table name
$tablename = 'tbl_test_question_matching';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores the matching type questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'subquestions' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'shuffleanswers' => array(
        'type' => 'text',
        'length' => 5,
        ),
);
// Other indicies
$name = 'questionidx';
$indexes = array(
    'fields' => array(
        'questionid' => array()
    )
);
?>