<?php

$tablename = 'tbl_ahis_survey';

$options = array('comment'=> 'table to store age group types','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'name' => array(
		'type' => 'text',
		'length' => 64,
        'notnull' => TRUE
		),
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_survey';

?>