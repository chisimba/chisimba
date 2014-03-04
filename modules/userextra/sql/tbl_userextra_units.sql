<?php

//define table
$tablename = 'tbl_userextra_units';
$options = array('comment'=>'Table to store units','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'unitcode'=>array('type'=>'text','not null'),
		'title'=>array('type'=>'text','not null')
		);
?>
