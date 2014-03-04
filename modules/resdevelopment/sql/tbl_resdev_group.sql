<?php
    $tablename = 'tbl_resdev_group';
    $options = array('comment' => 'Table for saving group information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'groupname' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'date_created' => array('type' => 'date', 'notnull'=>TRUE)
          );
?>