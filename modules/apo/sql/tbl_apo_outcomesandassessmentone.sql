<?php
    $tablename = 'tbl_apo_outcomesandassessmentone';
    $options = array('comment' => 'Table used to save data from user input in the outcomes and assessment one form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'd1a' => array('type' => 'text', 'notnull'=>TRUE),
                    'd1b' => array('type' => 'text', 'notnull'=>TRUE),
                    'd2a' => array('type' => 'text', 'notnull'=>TRUE),
                    'd2b' => array('type' => 'text', 'notnull'=>TRUE),
                    'd2c' => array('type' => 'text', 'notnull'=>TRUE),
                    'd3' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>