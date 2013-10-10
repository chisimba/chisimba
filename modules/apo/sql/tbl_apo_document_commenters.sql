<?php
    $tablename = 'tbl_apo_document_commenters';
    $options = array('comment' => 'Table used to save and update comments from different users', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'docid' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'userid' => array('type' => 'text', 'notnull'=>TRUE),
                    );
?>