<?php
// Table Name
$tablename = 'tbl_simpleregistrationcomments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table holding the participants comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
    'event_id' => array(
    'type' => 'text',
    'length' => 128
    ),
    'userid' => array(
    'type' => 'text',
    'length' => 128
    ),
    'comments' => array(
    'type' => 'text'
    ),
    'comment_date' =>  array(
      'type'  =>  'date'
      
    )
    );
?>
