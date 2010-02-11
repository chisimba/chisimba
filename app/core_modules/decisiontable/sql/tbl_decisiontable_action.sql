<?php
// Table Name
$tablename = 'tbl_decisiontable_action';

//Options line for comments, encoding and character set
$options = array('comment' => 'keeps a list of actions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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