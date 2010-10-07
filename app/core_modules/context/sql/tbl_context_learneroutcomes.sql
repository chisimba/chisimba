<?php

//Table name
$tablename = 'tbl_context_learneroutcomes';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to hold learner outcomes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE,
        ),
    'learningoutcome' => array(
        'type' => 'text',
        'notnull' => TRUE,
        ),
    'createdby' => array(
        'type' => 'text',
        'length' => 25,
        'notnull' => TRUE,
        ),
    'createdon' => array(
        'type' => 'timestamp'
        )
    );
?>
