<?php

//define table
$tablename = 'tbl_speak4free_articles';
$options = array('comment'=>'Table to store articles','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
                'topicid' => array('type' => 'text','length' => 32),
		'title'=>array('type'=>'text','length'=>512),
                'content'=>array('type'=>'text'))
;
?>
