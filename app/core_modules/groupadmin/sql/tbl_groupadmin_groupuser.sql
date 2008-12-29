<?php

// Table Name
$tablename = 'tbl_groupadmin_groupuser';

//Options line for comments, encoding and character set
$options = array('comment' => 'This is the bridge table between user and group table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'group_id' => array(
        'type' => 'text',
        'length' => 32,

        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,

        ),
    'last_updated' => array(
        'type' => 'date'
        ),
    'last_updated_by' => array(
        'type' => 'text',
        'length' => 32
        )
    );

//create other indexes here...

$name = 'ind_groupuser_FK';

$indexes = array(
                'fields' => array(
                    'group_id' => array(),
                    'user_id' => array(),
                )
        );
?>