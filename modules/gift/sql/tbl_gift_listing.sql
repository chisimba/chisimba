<?php

//define table
$tablename = 'tbl_gift_listing';
$options = array('comment'=>'Table to store Gifts','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'donor'=>array('type'=>'text','not null'),
		'userid'=>array('type'=>'text','length'=>32,'not null'),
		'giftname'=>array('type'=>'text','length'=>512,'not null'),
		'description'=>array('type'=>'text','not null'),
		'value'=>array('type'=>'integer','not null'),
		'listed'=>array('type'=>'boolean','not null'));
?>
