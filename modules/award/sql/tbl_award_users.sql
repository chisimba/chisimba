<?php

$tablename = "tbl_award_users";
$options = array('comment' => 'Bridging table for users and groups and additional user info', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
            'notnull' => TRUE
        ),
    'userid' => array(
       'type' => 'text',
       'length' => 32
       ),
    'tuid' => array(
       'type' => 'text',
       'length' => 32
       ),
    'position' => array(
       'type' => 'text',
       'length' => 64
       )
    );
    
$name = 'tbl_award_users_idx';

$indexes = array(
                'fields' => array(
                        'userid' => array(),
                        'tuid' => array()
        )

    );


?>