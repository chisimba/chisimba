<?php

//define table
$tablename = 'tbl_oer_sectionnodes';
$options = array('comment'=>'Table to store section nodes','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'section_id'=>array('type'=>'text','length'=>32,'not null'),
                'product_id'=>array('type'=>'text','length'=>32,'not null'),
                'title'=>array('type' => 'text','length' => 255),
                'status' => array('type' => 'text','length' => 32),
                'deleted' => array('type' => 'text','length' => 1),
                'level' => array('type' => 'text','length' => 1),
                'nodetype' => array('type' => 'text','length' => 32),
                'path' => array('type' => 'text'));
?>
