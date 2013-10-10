<?php
/*
$sqldata[]="INSERT INTO `tbl_lrs_default_continuity` VALUES
 ('1','0')";*/

$tablename = "tbl_award_default_continuity";
$options = array('comment' => 'Table to store default wage continuity data.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'continuity' => array(
	   'type' => 'float',
	   'length' => 4
	   ),
	);
	
$name = 'tbl_award_default_continuity_idx';	

$indexes = array(
                'fields' => array(
                	 'continuity' => array()
		)

	);
?>