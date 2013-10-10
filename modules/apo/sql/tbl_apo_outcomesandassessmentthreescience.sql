<?php
    $tablename = 'tbl_apo_outcomesandassessmentthreescience';
    $options = array('comment' => 'Table used to save data from user input in the outcomes and assessment three form for the science faculty', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'a1' => array('type' => 'text', 'notnull'=>TRUE),
                    'a2' => array('type' => 'text', 'notnull'=>TRUE),                    
                    'b1' => array('type' => 'text', 'notnull'=>TRUE),
                    'b2' => array('type' => 'text', 'notnull'=>TRUE),
                    'c1' => array('type' => 'text', 'notnull'=>TRUE),
                    'c2' => array('type' => 'text', 'notnull'=>TRUE),
                    'd1' => array('type' => 'text', 'notnull'=>TRUE),
                    'd8' => array('type' => 'text', 'notnull'=>TRUE),
                    'e1' => array('type' => 'text', 'notnull'=>TRUE),
                    'e8' => array('type' => 'text', 'notnull'=>TRUE),
                    'other' => array('type' => 'text', 'notnull'=>TRUE),
                    'f1' => array('type' => 'text', 'notnull'=>TRUE),
                    'f2' => array('type' => 'text', 'notnull'=>TRUE),
                    'f3' => array('type' => 'text', 'notnull'=>TRUE),
                    'g9' => array('type' => 'text', 'notnull'=>TRUE),
                    'g10' => array('type' => 'text', 'notnull'=>TRUE),
                    'h11' => array('type' => 'text', 'notnull'=>TRUE),
                    'h12' => array('type' => 'text', 'notnull'=>TRUE),
                    'd6' => array('type' => 'text', 'notnull'=>TRUE),
                    'd7' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>