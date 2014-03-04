<?php
    $tablename = 'tbl_apo_subsidyrequirements';
    $options = array('comment' => 'Table used to save data from user input in the subsidy requirements form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'c1' => array('type' => 'text', 'notnull'=>TRUE),
                    'c2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'c2b' => array('type' => 'text', 'notnull'=>TRUE),
                    'c3' => array('type' => 'text', 'notnull'=>TRUE),
                    'c4a' => array('type' => 'text', 'notnull'=>TRUE),
                    'c4b1' => array('type' => 'text', 'notnull'=>TRUE),
                    'c4b2' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>