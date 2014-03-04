<?php
/**
*
* A sample SQL file for imagegallery. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_imagegallery_images';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of images for the imagegallery module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'album_id' => array(
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
    'file_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'description' => array(
        'type' => 'text',
        'length' => 250,
    ),
    'caption' => array(
        'type' => 'text',
        'length' => 250,
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
    'view_count' => array(
        'type' => 'text',
        'length' => 10,
        'default' => 0,
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

$name = 'tbl_imagegallery_images_idx';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'gallery_id' => array(),
        'album_id' => array(),
        'user_id' => array(),
        'context_code' => array(),
        'file_id' => array(),
    )
);
?>