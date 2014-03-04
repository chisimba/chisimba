<?php
    $tablename = 'tbl_apo_faculties';
    $options = array('comment' => 'Table for saving faculty information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array(
                'id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'name' => array('type' => 'text', 'notnull'=>TRUE),
                'date_created' => array('type' => 'date', 'notnull'=>TRUE),
                'userid' => array('type' => 'text','length' => 15, 'notnull'=>TRUE),
                'contact_person' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'telephone' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                'deleted' => array('type' => 'text','length' => 1),
                'level' => array('type' => 'text','length' => 1),
                'path' => array('type' => 'text')
             );
?>