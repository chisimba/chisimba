<?php

$tablename = 'tbl_learningcontent_sessions';

// Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'sessionid' => array(
        'type' => 'text',
        'length' => 60,
        'notnull' => TRUE
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'starttime' => array(
        'type' => 'timestamp',
        'notnull' => FALSE
        ),
    'endtime' => array(
        'type' => 'timestamp',
        'notnull' => FALSE
        )
    );   
?>
