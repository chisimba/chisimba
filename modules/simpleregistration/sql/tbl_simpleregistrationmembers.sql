<?php
// Table Name
$tablename = 'tbl_simpleregistrationmembers';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table holding the participants details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
    'first_name' => array(
    'type' => 'text',
    'length' => 128
    ),
    'last_name' => array(
    'type' => 'text',
    'length' => 128
    ),
    'email' => array(
    'type' => 'text',
    'length' => 255
    ),
    'company' => array(
    'type' => 'text',
    'length' => 512
    ),
    'event_id' => array(
    'type' => 'text',
    'length' => 128
    ),
    'registration_date' =>  array(
      'type'  =>  'date'
      
    )
    );
?>
