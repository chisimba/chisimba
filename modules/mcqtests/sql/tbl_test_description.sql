<?php
//Table name
$tablename = 'tbl_test_description';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores descriptions for a given category', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'categoryid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'questionname' => array(
        'type' => 'text',
        'length' => 256,
        ),
    'questiontext' => array(
        'type' => 'text',
        ),
    'feedback' => array(
        'type' => 'text',
        ),
    'tags' => array(
        'type' => 'text',
        'length' => 256,
        ),
     'othertags' => array(
        'type' => 'text',
        'length' => 256,
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
        ),
    'sortorder' => array(
        'type' => 'text',
        'length' => 32,
        )
    );
?>