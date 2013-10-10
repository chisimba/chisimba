<?php
//Table name
$tablename = 'tbl_test_random_matching';

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
    'choose' => array(
        'type' => 'text',
        'length' => 255,
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