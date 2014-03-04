<?php
//Table name
$tablename = 'tbl_test_question_multianswers';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores the answers for multiple type questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
   'questionid' => array(
        'type' => 'text',
        'length' => 32
        ),
   'answer' => array(
        'type' => 'text',
        'length' => 255
        ),
   'correctanswer' => array(
        'type' => 'text',
        'length' => 32
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