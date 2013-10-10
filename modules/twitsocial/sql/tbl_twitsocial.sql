<?php
$tablename = 'tbl_twitsocial';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store twitter degrees of seperation', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => true
        ),
    'name' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'screen_name' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'location' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'image_url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'url' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'follow_count' => array(
        'type' => 'integer',
        'length' => 100,
        ),
    'parent_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'checked' => array(
        'type' => 'integer',
        'length' => 1,
        'default' => 0,
        ),
    'sntag' => array(
        'type' => 'integer',
        'length' => 1,
        'default' => 0,
        ),
    'loctag' => array(
        'type' => 'integer',
        'length' => 1,
        'default' => 0,
    ),
);

$name = 'parentid';

$indexes = array(
                'fields' => array(
                    'parent_id' => array(),
                   // 'screen_name' => array(),
                   // 'location' => array(),
                )
        );
?>