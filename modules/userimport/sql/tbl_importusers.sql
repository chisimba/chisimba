<?php
// Table Name
$tablename = 'tbl_importusers';
//Options line for comments, encoding and character set
$options = array('comment' => 'This table keeps track of users imported via the batch methods', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array( 'type' => 'text', 'length' => 32),
    'userId' => array('type'=>'text','length'=>25),
    'adminId' => array('type'=>'text','length'=>25),
    'contextCode' => array('type'=>'text','length'=>255),
    'creationDate' => array('type'=>'text','length'=>25), 
    'importMethod' => array('type'=>'text','length'=>32),
    'batchId' => array('type'=>'text','length'=>255),
    'r' => array('type'=>'text','length'=>32)
    );

?>
