<?php
$tablename = 'tbl_form_records';
$options = array('comment' => 'Forms data storage table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'form_id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'name' => array(
        'type' => 'text',
        'length' => 50
    ),
    'title' => array(
        'type' => 'text',
        'length' => 50
    ),
    'ip' => array(
        'type' => 'text',
        'length' => 30
    ),
    'browser' => array(
        'type' => 'text',
        'length' => 50
    ),
    'submitted' => array(
        'type' => 'timestamp'
    )
);
?>
