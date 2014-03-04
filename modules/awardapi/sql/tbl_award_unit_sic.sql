<?php
$tablename = "tbl_award_unit_sic";

$options = array('comment' => 'table to store a list of unit and sic id values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'unitid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'major_divid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'divid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'major_groupid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'groupid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'sub_groupid' => array(
	   'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
	   )
	);
  
$name = 'tbl_award_unitsic_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'groupid' => array(),
                	'major_groupid' => array(),
                	'sub_groupid' => array(),
                	'major_divid' => array(),
                	'divid' => array(),
                	'unitid' => array()
                )
        );
?>