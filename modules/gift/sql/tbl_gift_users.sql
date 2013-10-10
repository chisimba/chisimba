<?php

//define table
$tablename = 'tbl_gift_users';
$options = array('comment'=>'Table to log users','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'userid'=>array('type'=>'text','length'=>32,'not null'),
                'accepted'=>array('type'=>'text','length'=>1),
		'time' => array('type' => 'timestamp','notnull' => TRUE));

?>
