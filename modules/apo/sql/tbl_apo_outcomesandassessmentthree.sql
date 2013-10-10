<?php
    $tablename = 'tbl_apo_outcomesandassessmentthree';
    $options = array('comment' => 'Table used to save data from user input in the outcomes and assessment three form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'a' => array('type' => 'text', 'notnull'=>TRUE),
                    'b' => array('type' => 'text', 'notnull'=>TRUE),
                    'c' => array('type' => 'text', 'notnull'=>TRUE),
                    'd' => array('type' => 'text', 'notnull'=>TRUE),
                    'e' => array('type' => 'text', 'notnull'=>TRUE),
                    'f' => array('type' => 'text', 'notnull'=>TRUE),
                    'g' => array('type' => 'text', 'notnull'=>TRUE),
                    'h' => array('type' => 'text', 'notnull'=>TRUE),
                    'i' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>