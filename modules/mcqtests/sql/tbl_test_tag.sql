<?php
//Table name
$tablename = 'tbl_test_tag';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores tags', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'rawname' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'tagtype' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'description' => array(
        'type' => 'text',
        ),
    'descriptionformat' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'flag' => array(
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
?>