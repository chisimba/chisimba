<?php

//define table
$tablename = 'tbl_userextra_activation';
$options = array('comment'=>'Table to activated users','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'userid'=>array('type'=>'text','length' => 32)
		);
?>
