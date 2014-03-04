<?php
//Table name
$tablename = 'tbl_test_question_numerical';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores the numerical type questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
        'type'=>'integer',
        'length'=> 10
        ),
    'mark' => array(
        'type'=>'integer',
        'length'=> 10
        ),
    'tolerance' => array(
        'type'=>'text',
        'length'=>255
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