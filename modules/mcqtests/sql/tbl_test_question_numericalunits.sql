<?php
//Table name
$tablename = 'tbl_test_question_numericalunits';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores the numericalunits for questions of type numerical', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'multiplier' => array(
        'type'=>'text',
        'length'=>50
        ),
    'unit' => array(
        'type'=>'text',
        'length'=>50
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