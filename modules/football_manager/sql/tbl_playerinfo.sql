<?php

//define table
$tablename = 'tbl_playerinfo';
$options = array('comment'=>'test','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'firstname'=>array('type'=>'text','length'=>40),
		'lastname'=>array('type'=>'text','length'=>40),
		'age'=>array('type'=>'integer'),
		'position'=>array('type'=>'text','length'=>4),
		'transferfee'=>array('type'=>'double'),
		'otherinfo'=>array('type'=>'blob'),
		'status'=>array('type'=>'boolean'));
?>
