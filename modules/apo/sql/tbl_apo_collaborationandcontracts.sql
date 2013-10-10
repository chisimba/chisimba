<?php
    $tablename = 'tbl_apo_collaborationandcontracts';
    $options = array('comment' => 'Table used to save data from user input in the collaborations and contracts form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'f1a' => array('type' => 'text', 'notnull'=>TRUE),
                    'f1b' => array('type' => 'text'),
                    'f2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'f2b' => array('type' => 'text'),
                    'f3a' => array('type' => 'text', 'notnull'=>TRUE),
                    'f3b' => array('type' => 'text'),
                    'f4' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>