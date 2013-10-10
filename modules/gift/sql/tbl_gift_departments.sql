<?php

//define table
$tablename = 'tbl_gift_departments';
$options = array('comment'=>'Table to store departments','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'name'=>array('type'=>'text','length'=>255,'not null'),
                'deleted' => array('type' => 'text','length' => 1),
                'level' => array('type' => 'text','length' => 1),
               'path' => array('type' => 'text'));
?>
