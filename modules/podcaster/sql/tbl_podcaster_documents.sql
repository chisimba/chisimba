<?php
    $tablename = 'tbl_podcaster_documents';
    $options = array('comment' => 'Table for saving podcast information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docname' => array('type' => 'text', 'notnull'=>TRUE),
                    'date_created' => array('type' => 'date', 'notnull'=>TRUE),
                    'userid' => array('type' => 'text','length' => 15, 'notnull'=>TRUE),
                    'refno' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'topic' => array('type' => 'text', 'notnull'=>TRUE),
                    'department' => array('type' => 'text','length' => 128, 'notnull'=>TRUE),
                    'contact_person' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'telephone' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'groupid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'ext' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'mode' => array('type' => 'text','length' => 10, 'notnull'=>TRUE),
                    'active' => array('type' => 'text','length' => 1, 'notnull'=>TRUE),
                    'upload' => array('type' => 'text','length' => 1, 'notnull'=>TRUE),
                    'currentuserid' => array('type' => 'text','length' => 10),
                    'deleteDoc'=>array('type' => 'text','length' => 1),
                    'rejectDoc'=>array('type' => 'text','length' => 1),
                    'version' => array('type' => 'text','length' => 2, 'notnull'=>TRUE, 'default'=>'1'),
                    'ref_version' => array('type' => 'text','length' => 10, 'notnull'=>TRUE, 'default'=>'1'),
                    'status' => array('type' => 'text','length' => 1, 'notnull'=>TRUE, 'default'=>'0'));
?>