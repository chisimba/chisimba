<?php

$tablename = 'tbl_contextcontent_activitystreamer';

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
    'modulecode' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'contextitemid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'datecreated' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'description' => array(
        'type' => 'text',
        'notnull' => TRUE
        ),
    'starttime' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'endtime' => array(
        'type' => 'timestamp',
        'notnull' => FALSE
        ),
    'pageorchapter' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )
    );
?>
