<?php
//Table name
$tablename = 'tbl_test_tag_instance';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table stores tag instances', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'tagid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'itemtype' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'itemid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'tiuserid' => array(
        'type' => 'text',
        'length' => 32,
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
$name = 'tag_instance_indicies';
$indexes = array(
    'fields' => array(
        'tagid' => array(),
        'itemid' => array()
    )
);
?>