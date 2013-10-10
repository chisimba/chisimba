<?php

//define table
$tablename = 'tbl_oer_groups';
$options = array('comment'=>'Table to store groups','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32,'not null'),
                'contextcode' =>array('type' =>'text','length'=>32),
                'email' =>array('type' =>'text','length'=>255),
                'address' =>array('type' =>'text','length'=>255),
                'city' =>array('type' =>'text','length'=>255),
                'state' =>array('type' =>'text','length'=>255),
                'postalcode' =>array('type' =>'text','length'=>255),
                'website' =>array('type' =>'text','length'=>255),
                'loclat' =>array('type' =>'text','length'=>255),
                'loclong' =>array('type' =>'text','length'=>255),
                'country' =>array('type' =>'text','length'=>255),
                'region' =>array('type' =>'text','length'=>255),
                'thumbnail' =>array('type' => 'text', 'length' =>255),
                'interests' =>array('type' => 'text', 'length' =>255),
                'parent_id'=> array('type' => 'text','length' => 32)
                        
		
);
?>