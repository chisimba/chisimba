<?php
$tablename = 'tbl_twitsocial_sn';

//Options line for comments, encoding and character set
$options = array('comment' => 'screen name tags', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => true
        ),
    'screen_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'weight' => array(
        'type' => 'integer',
        'length' => 50,
        ),
);

$name = 'location';

$indexes = array(
                'fields' => array(
                    //'screen_name' => array(),
                    //'weight' => array(),
                )
        );
?>