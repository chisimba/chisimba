<?php
    $tablename = 'tbl_apo_documentcomments';
    $options = array('comment' => 'Table used to save and update comments from different users', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'comments' => array('type' => 'text'),
                    'userid' => array('type' => 'text', 'notnull'=>TRUE),
                    'comment_time' => array('type' => 'date', 'notnull'=>TRUE),
                    );
?>