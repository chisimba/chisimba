<?php

//define table
$tablename = 'tbl_schoolregisterusers_userextra';
$options = array('comment'=>'Table to store user data for schools',
  'collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
    'id' => array('type' => 'text','length' => 32),
    'parentid' => array('type' => 'text','length' => 32),
    'middlename' =>array('type' =>'text','length'=>255),
    'birthdate' =>array('type' =>'text','length'=>255),
    'address' =>array('type' =>'text','length'=>255),
    'city' =>array('type' =>'text','length'=>255),
    'state' =>array('type' => 'text', 'length' =>255),
    'postalcode'=>array('type'=>'text','length'=>255),
    'school' => array('type' => 'text','length' =>255),
    'description' =>array('type' =>'text','length'=>255)
);
?>