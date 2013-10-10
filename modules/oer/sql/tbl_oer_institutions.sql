<?php

//define table
$tablename = 'tbl_oer_institutions';
$options = array('comment'=>'Table to store institution','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
    'id'            => array('type' => 'text', 'length' => 32),
    'name'          => array('type' => 'text', 'length' => 128),
    'description'   => array('type' => 'clob'),
    'type'          => array('type' => 'text', 'length' => 32),
    'country'       => array('type' => 'text', 'length' => 32),
    'address1'      => array('type' => 'text', 'length' => 50),
    'address2'      => array('type' => 'text', 'length' => 50),
    'address3'      => array('type' => 'text', 'length' => 50),
    'zip'           => array('type' => 'integer'),
    'city'          => array('type' => 'text', 'length' => 50),
    'websitelink'   => array('type' => 'text', 'length' => 100),
    'keyword1'      => array('type' => 'text', 'length' => 32),
    'keyword2'      => array('type' => 'text', 'length' => 32),
    'thumbnail'     => array('type' => 'text', 'length' => 255)
);
?>

