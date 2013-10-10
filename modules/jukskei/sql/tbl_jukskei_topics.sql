<?php

//define table
$tablename = 'tbl_jukskei_topics';
$options = array('comment'=>'Table to store groups','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'title'=>array('type'=>'text','length'=>512),
                'active' => array('type' => 'text','length' => 10),
                'content'=>array('type'=>'text'))
;
?>
