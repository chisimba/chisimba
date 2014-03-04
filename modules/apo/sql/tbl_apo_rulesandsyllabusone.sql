<?php
    $tablename = 'tbl_apo_rulesandsyllabusone';
    $options = array('comment' => 'Table used to save data from user input in the rules and syllabus one form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'b1' => array('type' => 'text', 'notnull'=>TRUE),
                    'b2' => array('type' => 'text', 'notnull'=>TRUE),
                    'b3a' => array('type' => 'text', 'notnull'=>TRUE),
                    'b3b' => array('type' => 'text', 'notnull'=>TRUE),
                    'b4a' => array('type' => 'text', 'notnull'=>TRUE),
                    'b4b' => array('type' => 'text', 'notnull'=>TRUE),
                    'b4c' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>