<?php
//Table name
$tablename = 'tbl_test_tag_correlation';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores tag correlation', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'tagid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'correlatedtags' => array(
        'type' => 'text'
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
$name = 'tagidx';
$indexes = array(
    'fields' => array(
        'tagid' => array()
    )
);
?>