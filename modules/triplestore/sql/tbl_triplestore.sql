<?php
// Table Name
$tablename = 'tbl_triplestore';

//Options line for comments, encoding and character set
$options = array('comment' => 'Generic semantic web storage', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'date' => array(
        'type' => 'timestamp',
        ),
    'subject' => array(
        'type' => 'text',
        'length' => 32
        ),
    'predicate' => array(
        'type' => 'text',
        'length' => 32
        ),
    'object' => array(
        'type' => 'text',
        'length' => 16384
        )
    );

?>
