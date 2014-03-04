<?php

//define table
$tablename = 'tbl_oer_downloaders';
$options = array('comment'=>'Table to store downloader details','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32,'not null'),
                'fname' =>array('type' =>'text','length'=>255),
                'lname' =>array('type' =>'text','length'=>255),
                'email' =>array('type' =>'text','length'=>255),
                'organisation' =>array('type' =>'text','length'=>255),
                'occupation' =>array('type' =>'text','length'=>255),                
                'downloadreason' =>array('type' =>'text'),
                'notifyoriginal' =>array('type' =>'text','length'=>32),
                'notifyadaptation' =>array('type' =>'text','length'=>32),
                'productid' =>array('type' =>'text','length'=>32),
                'useterms' =>array('type' =>'text','length'=>32),
                'downloadformat' =>array('type' =>'text','length'=>32),
                'downloadtime' =>array('type' =>'text','length'=>32)
);
?>