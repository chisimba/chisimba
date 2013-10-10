<?php
// Table Name
$tablename = 'tbl_bmplus_words';

//Options line for comments, encoding and character set
$options = array('comment' => 'brand plus words', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'word' => array(
        'type' => 'text',
        'length' => 140
    ),
    'occurances' => array(
        'type' => 'text',
        'length' => 50,
    ),
);
?>