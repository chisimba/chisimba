<?php
// Table for holding the blog posts

//Table Name
$tablename = 'tbl_simpleblog_blogs';

//Options line for comments, encoding and character set
$options = array('comment' => 'This table holds content data for the simpleblog module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'blogid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'datecreated' => array(
            'type' => 'timestamp',
            ),
    'modifierid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'datemodified' => array(
            'type' => 'timestamp',
        ),
    'blogtype' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'blog_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'blog_description' => array(
        'type' => 'clob',
        ),
);

// Indexes for the blogs table
$name = 'tbl_simpleblog_blogs_idx';

$indexes = array(
    'fields' => array(
        'userid' => array(),
        'blogid' => array(),
        'blogtype' => array(),
     )
);
?>