<?php

//define table
$tablename = 'tbl_rtt_jnlp';
$options = array('comment'=>'Table to Jnlp Key entries','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'userid'=>array('type'=>'text','length'=>32,'not null'),
		'jnlp_key'=>array('type'=>'text','length'=>128,'not null'),
		'jnlp_value'=>array('type'=>'text','length'=>128,'not null'),
                'createdon'=>array('type'=>'timestamp')
);
?>
