<?php
$tablename = "tbl_award_index_summary";

$options = array('comment' => 'Table to store award index summary', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'indexid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'summary' => array(
		'type' => 'clob',
		)
	);
  
$name = 'tbl_award_index_summary_idx';

$indexes = array(
                'fields' => array(
                	'indexid' => array()
                )
            );


?>