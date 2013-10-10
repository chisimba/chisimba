<?php
    $tablename = 'tbl_resdev_attendance';
    $options = array('comment' => 'Table for saving attendance information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
    $fields = array('id' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'attendance' => array('type' => 'text','length' => 32, 'notnull'=>TRUE),
                    'date_created' => array('type' => 'date', 'notnull'=>TRUE)
          );
?>