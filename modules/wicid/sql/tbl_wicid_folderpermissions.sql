<?php
    $tablename = 'tbl_wicid_folderpermissions';
    $options = array('comment' => 'Table for holder folder permissions information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'folderpath' => array('type' => 'text', 'notnull'=>TRUE),
                    'userid' => array('type' => 'text','length' => 15, 'notnull'=>TRUE),
                     'viewfiles' => array('type' => 'text','length' => 5, 'notnull'=>TRUE),
                     'uploadfiles' => array('type' => 'text','length' => 5, 'notnull'=>TRUE),
                     'createfolder' => array('type' => 'text','length' => 5, 'notnull'=>TRUE),
                    );
?>