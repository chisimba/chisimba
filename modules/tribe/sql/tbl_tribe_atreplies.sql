<?php
// Table Name
$tablename = 'tbl_tribe_atreplies';

//Options line for comments, encoding and character set
$options = array('comment' => 'tribe replies', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'toid' => array(
        'type' => 'text',
        'length' => 32
    ),
    'msgid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'fromid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'datesent' => array(
        'type' => 'timestamp',
        ),
    'tribegroup' => array(
        'type' => 'text',
        'length' => 50,
        ),
    );
?>