<?php
    $tablename = 'tbl_wicid_fileuploads';
    $options = array('comment' => 'Table for saving file information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'filename' => array('type' => 'text', 'notnull'=>TRUE),
                    'filetype' => array('type' => 'text','length' => 128, 'notnull'=>TRUE),
                    'date_uploaded' => array('type' => 'timestamp', 'notnull'=>TRUE),
                    'userid' => array('type' => 'text','length' => 15, 'notnull'=>TRUE),
                    'refno' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'parent' => array('type' => 'text', 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'filepath' => array('type' => 'text', 'notnull'=>TRUE));
?>