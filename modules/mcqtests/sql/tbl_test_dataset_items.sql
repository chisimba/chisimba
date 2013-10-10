<?php
//Table name
$tablename = 'tbl_test_dataset_items';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores test dataset-items', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'datasetid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'itemnumber' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'value' => array(
        'type' => 'text',
        'length' => 255,
        )
    );
// Other indicies
$name = 'datasetidx';
$indexes = array(
    'fields' => array(
        'datasetid' => array()
    )
);
?>