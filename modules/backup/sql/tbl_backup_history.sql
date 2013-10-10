<?php
/**
*
* A sample SQL file for backup. Please adapt this to your requirements.
*
*/
// Table Name
$tablename = 'tbl_backup_history';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of records of backups made', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'datecreated' => array(
        'type' => 'timestamp'
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
);

?>