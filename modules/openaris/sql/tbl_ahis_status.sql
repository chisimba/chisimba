<?php

$tablename = 'tbl_ahis_status';

$options = array('comment'=> 'table to store statuses','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	
    'name' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		)
	
    );
//create other indexes here...

$name = 'index_tbl_ahis_status';

?>