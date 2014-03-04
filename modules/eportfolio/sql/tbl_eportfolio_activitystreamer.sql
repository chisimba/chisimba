<?php

$tablename = 'tbl_eportfolio_activitystreamer';

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
    'owneruserid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'groupid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sessionid' => array(
        'type' => 'text',
        'length' => 60,
        'notnull' => TRUE
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 25,
        ),
    'modulecode' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'partname' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
        ),
    'recordid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'description' => array(
        'type' => 'text',
        'notnull' => TRUE
        ),
    'datecreated' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'starttime' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'endtime' => array(
        'type' => 'timestamp',
        )
    );
?>
