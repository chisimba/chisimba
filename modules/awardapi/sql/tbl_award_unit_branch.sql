<?php
$tablename = "tbl_award_unit_branch";

$options = array('comment' => 'table to store a list of unit and branch id values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'branchid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'unitid' => array(
	   'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
	   )
	);
  
$name = 'tbl_award_unit_branch_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'branchid' => array(),
                	'unitid' => array()
                )
        );
?>