<?php
    $tablename = 'tbl_apo_contactdetails';
    $options = array('comment' => 'Table used to save data from user input in the contact details form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'h1' => array('type' => 'text', 'notnull'=>TRUE),
                    'h2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'h2b' => array('type' => 'text', 'notnull'=>TRUE),
                    'h3a' => array('type' => 'text', 'notnull'=>TRUE),
                    'h3b' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>