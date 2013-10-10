<?php
$tablename = "tbl_award_socsubmajorgroup";

$options = array('comment' => 'table to store a list of soc sub-major groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'major_groupid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
	   'type' => 'text',
	   'length' => 96
	   )
	);
	
$name = 'tbl_award_socsubmajorgroup_idx';

$indexes = array(
                'fields' => array(
                	'major_groupid' => array()
                )
        );

?>