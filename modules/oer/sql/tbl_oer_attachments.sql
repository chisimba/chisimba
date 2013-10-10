<?php

//define table
$tablename = 'tbl_oer_attachments';
$options = array('comment'=>'Table to store attachments','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
$fields = array(
		'id' => array('type' => 'text','length' => 32),
                'section_id' => array('type' => 'text','length' => 32),
		'name'=>array('type'=>'text','length'=>255,'not null'),
                'deleted' => array('type' => 'text','length' => 1)
);
?>
