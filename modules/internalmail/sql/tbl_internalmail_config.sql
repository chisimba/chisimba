<?php
//5ive definition
$tablename = 'tbl_internalmail_config';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing email configuration settings.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'surname_first' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'hide_username' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'default_folder_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'auto_delete' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'signature' => array(
        'type' => 'clob',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_config_index';

$indexes = array(
                'fields' => array(
                    'user_id' => array(),
                ),
        );
?>