<?php
$tablename = "tbl_award_socunitgroup";

$options = array('comment' => 'table to store a list of soc unit groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'submajor_groupid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'minor_groupid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'description' => array(
	   'type' => 'text',
	   'length' => 96
	   )
	);
	
$name = 'tbl_award_socunitgroup_idx';

$indexes = array(
                'fields' => array(
                	'major_groupid' => array(),
                    'submajor_groupid' => array(),
                    'minor_groupid' => array()
                )
        );

?>