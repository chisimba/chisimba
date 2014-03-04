<?php
    $tablename = 'tbl_apo_review';
    $options = array('comment' => 'Table used to save data from user input in the review form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'g1a' => array('type' => 'text', 'notnull'=>TRUE),
                    'g1b' => array('type' => 'text', 'notnull'=>TRUE),
                    'g2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'g2b' => array('type' => 'text', 'notnull'=>TRUE),
                    'g3a' => array('type' => 'text', 'notnull'=>TRUE),
                    'g3b' => array('type' => 'text', 'notnull'=>TRUE),
                    'g4a' => array('type' => 'text', 'notnull'=>TRUE),
                    'g4b' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>