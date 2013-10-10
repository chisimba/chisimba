<?php
//Table name
$tablename = 'tbl_test_randomshortansmatch';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores Random short-answer matching questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'choose' => array(
        'type' => 'text',
        'length' => 256,
        ),
    'sortorder' => array(
        'type' => 'text',
        'length' => 32,
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
// Other indicies
$name = 'questionidx';
$indexes = array(
    'fields' => array(
        'questionid' => array()
    )
);
?>