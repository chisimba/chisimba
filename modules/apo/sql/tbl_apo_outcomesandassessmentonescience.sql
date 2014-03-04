<?php
    $tablename = 'tbl_apo_outcomesandassessmentonescience';
    $options = array('comment' => 'Table used to save data from user input in the outcomes and assessment one form for the science faculty', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'd1' => array('type' => 'text', 'notnull'=>TRUE),
                    'd21' => array('type' => 'text', 'notnull'=>TRUE),
                    'd22' => array('type' => 'text', 'notnull'=>TRUE),
                    'd23' => array('type' => 'text', 'notnull'=>TRUE),
                    'd24' => array('type' => 'text', 'notnull'=>TRUE),
                    'd25' => array('type' => 'text', 'notnull'=>TRUE),
                    'd3' => array('type' => 'text', 'notnull'=>TRUE),
                    'd4' => array('type' => 'text', 'notnull'=>TRUE)
                    );
?>