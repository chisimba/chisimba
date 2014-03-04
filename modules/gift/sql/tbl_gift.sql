<?php

//define table
$tablename = 'tbl_gift';
$options = array('comment'=>'Table to store Gifts','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'donor'=>array('type'=>'text','length'=>128,'not null'),
		'recipient'=>array('type'=>'text','length'=>128,'not null'),
		'giftname'=>array('type'=>'text','length'=>128,'not null'),
		'description'=>array('type'=>'blob','not null'),
		'value'=>array('type'=>'integer','not null'),
                'gift_type' => array('type' => 'text','length' => 32),
                'tran_date'=>array('type'=>'timestamp'),
                'date_recieved'=>array('type'=>'timestamp'),
		'listed'=>array('type'=>'boolean','not null'),
                'division' => array('type' => 'text','length' => 32),
                'comments' => array('type' => 'text'),
                'deleted' => array('type' => 'text','length' => 1)
);
?>
