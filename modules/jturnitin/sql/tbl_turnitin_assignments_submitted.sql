<?php
// Table Name
$tablename = 'tbl_turnitin_assignments_submitted';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold assigment details which was submitted to Turnitin by students', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'objectid' => array(
        'type' => 'text',
        'length' => 32,
        ),       
    'userid' => array(
        'type' => 'text',
        'length' => 32,
        )  ,
'assigntitle' => array(
        'type' => 'text',
        'length' => 255,
        )  ,
 'submitted' => array(
        'type' => 'text',
        'length' => 1,
        )  ,
  'contextcode' => array(
        'type' => 'text',
        'length' => 32,
        ),
    );

?>
