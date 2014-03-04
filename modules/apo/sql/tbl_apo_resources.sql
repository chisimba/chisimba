<?php
    $tablename = 'tbl_apo_resources';
    $options = array('comment' => 'Table used to save data from user input in the resources form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'e1a' => array('type' => 'text', 'notnull'=>TRUE),
                    'e1b' => array('type' => 'text', 'notnull'=>TRUE),
                    'e2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'e2b' => array('type' => 'text', 'notnull'=>TRUE),
                    'e2c' => array('type' => 'text', 'notnull'=>TRUE),
                    'e3a' => array('type' => 'text', 'notnull'=>TRUE),
                    'e3b' => array('type' => 'text', 'notnull'=>TRUE),
                    'e3c' => array('type' => 'text', 'notnull'=>TRUE),
                    'e4' => array('type' => 'text', 'notnull'=>TRUE),
                    'e5a' => array('type' => 'text', 'notnull'=>TRUE),
                    'e5b' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>