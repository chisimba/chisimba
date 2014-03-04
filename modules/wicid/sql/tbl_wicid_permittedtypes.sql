<?php
    $tablename = 'tbl_wicid_permittedtypes';
    $options = array('comment' => 'Table for saving permitted types file', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'ext' => array('type' => 'text','length' => 128, 'notnull'=>TRUE),
                    'name' => array('type' => 'text','length' => 256, 'notnull'=>TRUE),
                    'permitted' => array('type' => 'boolean', 'default'=> '0', 'notnull'=>TRUE),);
?>