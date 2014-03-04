<?php
// Table Name
$tablename = 'tbl_twitterizer_subs';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold twitterizer XMPP subscribers', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'jid' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'status' => array(
        'type' => 'text',
        'length' => 1,
    ),
    'datesent' => array(
        'type' => 'timestamp',
    ),
);
?>
