<?php
// Table Name
$tablename = 'tbl_decisiontable_rule';

//Options line for comments, encoding and character set
$options = array('comment' => 'able used to keep a list of rules.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'name' => array(
        'type' => 'text',
        'length' => 50
        )
    );
?>