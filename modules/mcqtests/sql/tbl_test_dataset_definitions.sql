<?php
//Table name
$tablename = 'tbl_test_dataset_definitions';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores test dataset-definitions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'datasetid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'categoryid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'type' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'options' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'itemcount' => array(
        'type' => 'text',
        'length' => 32,
        )
    );
// Other indicies
$name = 'definitions_index';
$indexes = array(
    'fields' => array(
        'categoryid' => array(),
        'datasetid' => array()
    )
);
?>