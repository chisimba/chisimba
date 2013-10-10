<?php
// Table Name
$tablename = 'tbl_jabberblog';

//Options line for comments, encoding and character set
$options = array('comment' => 'jabberblog data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'msgtype' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'msgfrom' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'msgbody' => array(
        'type' => 'clob',
        ),
    'datesent' => array(
        'type' => 'timestamp',
        ),
    'twitthreadid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    );
?>