<?php
    $tablename = 'tbl_apo_outcomesandassessmenttwo';
    $options = array('comment' => 'Table used to save data from user input in the outcomes and assessment two form', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'id1' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id2' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id3' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id4' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id5' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id6' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id7' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE),
                    'id8' => array('type' => 'integer','length'=>1, 'notnull'=>TRUE)
                    );
?>