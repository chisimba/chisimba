<?php
//Table name
$tablename = 'tbl_test_question_numericaloptions';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores the numerical type question options', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'instructionsformat' => array(
        'type'=>'text',
        'length'=> 2
        ),
    'instructions' => array(
        'type'=>'text',
        'length'=> 255
        ),
    'showunits' => array(
        'type'=>'text',
        'length'=> 5
        ),
    'unitgradingtype' => array(
        'type'=>'text',
        'length'=> 5
        ),
    'unitpenalty' => array(
        'type'=>'float'
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