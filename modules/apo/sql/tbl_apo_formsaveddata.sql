<?php
    $tablename = 'tbl_apo_formsaveddata';
    $options = array('comment' => 'Table used to save data from user input', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'formname' => array('type' => 'text', 'length' => 100, 'notnull'=>TRUE),
                    'content' => array('type' => 'text', 'notnull'=>TRUE),
                    );
?>