<?php
// Table Name
$tablename = 'tbl_language_modules';

//Options line for comments, encoding and character set
$options = array('comment' => 'language modules','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'module_id' => array(
        'type' => 'text',
        'length' => 50,

        ),
    'code' => array(
        'type' => 'text',
        'length' => 50,

        )
    );

?>