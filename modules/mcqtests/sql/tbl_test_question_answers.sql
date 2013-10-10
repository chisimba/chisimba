<?php
//table definition
$tablename = 'tbl_test_question_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table lists the answers and comments for the questions in a test', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'testid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'answer' => array(
        'type' => 'text',
        ),
    'answerformat' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'fraction' => array(
        'type' => 'decimal',
        ),
    'feedback' => array(
        'type' => 'text',
        ),
    'feedbackformat' => array(
        'type' => 'text',
        'length' => 2,
        ),
    );
// Other indicies
$name = 'answers_indicies';
$indexes = array(
    'fields' => array(
        'testid' => array(),
        'questionid' => array()
    )
);
?>
