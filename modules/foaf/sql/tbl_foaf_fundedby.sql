<?php
// Table Name
$tablename = 'tbl_foaf_fundedby';

//Options line for comments, encoding and character set
$options = array('comment' => 'FOAF Funders', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'funderurl' => array(
        'type' => 'text',
        'length' => 255,
        ),
    );

    //create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                    'userid' => array(),
                )
        );
?>