<?php
/**
*
* A sample SQL file for imagegallery. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_imagegallery_albums';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of albums for the imagegallery module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'gallery_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'context_code' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'title' => array(
        'type' => 'text',
        'length' => 250,
    ),
    'description' => array(
        'type' => 'text',
        'length' => 250,
    ),
    'cover_image_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'is_shared' => array(
        'type' => 'integer',
        'length' => 1,
        'default' => 0,
    ),
    'display_order' => array(
        'type' => 'integer',
        'length' => 1,
    ),
    'date_created' => array(
        'type' => 'timestamp'
    ),
    'created_by' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'date_updated' => array(
        'type' => 'timestamp'
    ),
    'updated_by' => array(
        'type' => 'text',
        'length' => 32,
    ),
);

//create other indexes here...

$name = 'tbl_imagegallery_albums_idx';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'gallery_id' => array(),
        'user_id' => array(),
        'context_code' => array(),
    )
);
?>