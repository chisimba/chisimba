<?php
// Table Name
$tablename = 'tbl_languagelist';

//Options line for comments, encoding and character set
$options = array('commment' => 'Holds the list of languages that KEWL has', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'languageCode' => array(
        'type' => 'text',
        'length' => 100,

        ),
    'languageName' => array(
        'type' => 'text',
        'length' => 100,

        ),
    );

?>