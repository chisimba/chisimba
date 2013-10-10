<?php
/**
*
* A sample SQL file for imagegallery. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_imagegallery_comments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of comments for the imagegallery module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'image_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'comment_text' => array(
        'type' => 'text',
        'length' => 250,
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

$name = 'tbl_imagegallery_coments_idx';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'image_id' => array(),
        'user_id' => array(),
    )
);
?>