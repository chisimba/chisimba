<?php

$tablename = "tbl_award_decent_work_category";

$options = array('comment' => 'Table to store decent work categories.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
            'type' => 'text',
            'length' => 32,
            'notnull' => TRUE
            ),
	'category' => array(
            'type' => 'text',
            'length' => 64,
            )
	);
	
$name = 'tbl_award_decent_work_category_idx';

?>