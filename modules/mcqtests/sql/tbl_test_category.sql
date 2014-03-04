<?php
//Table name
$tablename = 'tbl_test_category';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores test categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'parentcategoryid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'name' => array(
        'type' => 'text',
        ),
    'categoryinfo' => array(
        'type' => 'text',
        ),
    'infoformat' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sortorder' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'contextcode' => array(
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