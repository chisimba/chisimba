<?php
$tablename = 'tbl_test_question_calculated';
$options = array('comment' => 'This table lists the calculated questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'answer' => array(
        'type' => 'text',
        'length' => 100
        ),
    'tolerance' => array(
        'type' => 'text',
        'length' => 20
        ),
    'tolerancetype' => array(
        'type' => 'text',
        'length' => 10
        ),
    'correctanswerlength' => array(
        'type' => 'text',
        'length' => 10
        ),
    'correctanswerformat' => array(
        'type' => 'text',
        'length' => 10
        )
);
// Other indicies
$name = 'questionidx';
$indexes = array(
    'fields' => array(
        'questionid' => array()
    )
);
?>