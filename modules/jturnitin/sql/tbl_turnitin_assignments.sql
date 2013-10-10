<?php
// Table Name
$tablename = 'tbl_turnitin_assignments';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold assigment details which was submitted to Turnitin', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 32,
        ),       
    'submissionid' => array(
        'type' => 'text',
        'length' => 32,
        ),
  
    'title' => array(
        'type' => 'clob',
    	),  
    'instructions' => array(
        'type' => 'clob',
    	),  
    'instructoremail' => array(
        'type' => 'clob',
    	),
    'duedate' => array(
        'type' => 'timestamp',
    	),

    'datestart' => array(
        'type' => 'timestamp',
    	),
'resubmit' => array(
        'type' => 'text',
        'length' => 1,
        ),
    );

?>
