<?php
//Table name
$tablename = 'tbl_test_datasets';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores test datasets', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'datasetdefinition' => array(
        'type' => 'text',
        'length' => 255,
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