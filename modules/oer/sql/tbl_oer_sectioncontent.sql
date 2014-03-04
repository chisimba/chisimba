<?php
//define table
$tablename = 'tbl_oer_sectioncontent';
$options = array('comment'=>'Table to store section content','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
		'node_id'=>array('type'=>'text','length'=>32,'not null'),
                'title'=>array('type' => 'text','length' => 255),
                'content' => array('type' => 'text'),
                'status' => array('type' => 'text','length' => 12),
                'contributedby' => array('type' => 'text'),
                'userid'=>array('type' => 'text','length' => 32),
                'deleted'=>array('type' => 'text','length' => 1),
                'keywords' => array('type' => 'text', 'length' => 255),
                'adaptation_notes' => array('type' => 'text'),
                'current_path' => array('type' => 'text', 'length' => 255)
                );
?>