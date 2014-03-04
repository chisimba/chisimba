<?php

//define table
$tablename = 'tbl_oeruserdata_userextra';
$options = array('comment'=>'Table to store user extra INFO',
  'collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
    'id' => array('type' => 'text','length' => 32),
    'parentid' => array('type' => 'text','length' => 32),
    'birthdate' =>array('type' =>'text','length'=>255),
    'address' =>array('type' =>'text','length'=>255),
    'city' =>array('type' =>'text','length'=>255),
    'state' =>array('type' => 'text', 'length' =>255),
    'postalcode'=>array('type'=>'text','length'=>255),
    'orgcomp' => array('type' => 'text','length' =>255),
    'jobtitle'=>array('type'=>'text','length'=>255),
    'occupationtype' => array('type' => 'text','length' => 255),
    'workphone' =>array('type' =>'text','length'=>255),
    'description' =>array('type' =>'text','length'=>255),
    'website' => array('type' => 'text','length' => 255)
);
?>