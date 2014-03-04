<?php
// Table Name
$tablename = 'tbl_foaf_links';

//Options line for comments, encoding and character set
$options = array('comment' => 'FOAF related links', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'title' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255,
        )

    );

    //create other indexes here...



$indexes = array(
                'fields' => array(
                    'url' => array(),
                )
        );
?>
