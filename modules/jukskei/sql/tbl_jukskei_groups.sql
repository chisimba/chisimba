<?php

//define table
$tablename = 'tbl_jukskei_groups';
$options = array('comment'=>'Table to store groups','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'topicid'=>array('type'=>'text','length'=>32),
                'userid'=>array('type'=>'text','length'=>32));

?>
