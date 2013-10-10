<?php
// Table Name
$tablename = 'tbl_mxitpress_posts';

//Options line for comments, encoding and character set
$options = array('comment' => 'post data', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'msgtitle' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'msgbody' => array(
        'type' => 'clob',
        ),
    'datesent' => array(
        'type' => 'timestamp',
        ),
    );
?>
