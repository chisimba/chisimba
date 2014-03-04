<?php

//define table
$tablename = 'tbl_hiddenlangs';
$options = array('comment'=>'Table to hidden languages','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'langid'=>array('type'=>'text','length'=>128,'not null')
);
?>
