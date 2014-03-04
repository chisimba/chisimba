<?php
    $tablename = 'tbl_wicid_forward';
    $options = array('comment' => 'Table used to save data from user input', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('firstname'=>array('type' => 'text', 'notnull'=>TRUE),
                    'surname'=>array('type' => 'text', 'notnull'=>TRUE),                   
                    'email'=>array('type' => 'text', 'notnull'=>TRUE),
                    'link'=>array('type' => 'text', 'notnull'=>TRUE), 
                    'id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid'=>array('type'=> 'text', 'length'=>32, 'notnull'=>TRUE)
                    );
?>