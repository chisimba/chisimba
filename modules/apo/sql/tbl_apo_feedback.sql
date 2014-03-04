<?php
    $tablename = 'tbl_apo_feedback';
    $options = array('comment' => 'Table for saving feedback form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array(
                'id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'q1' => array('type' => 'text'),
                'q2' => array('type' => 'text'),
                'q3' => array('type' => 'text','length' => 15)
             );
?>