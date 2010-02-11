<?php

// Table Name
$tablename = 'tbl_decisiontable_decisiontable_action';

//Options line for comments, encoding and character set
$options = array('comment' => 'table used to keep a list of actions & decision tables', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'actionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'decisiontableid' => array(
        'type' => 'text',
        'length' => 32,
        )
    );

//create other indexes here...

$name = 'decisiontableid';

$indexes = array(
                'fields' => array(
                    'decisiontableid' => array(),
                    'actionid' => array()
                )
        );
?>