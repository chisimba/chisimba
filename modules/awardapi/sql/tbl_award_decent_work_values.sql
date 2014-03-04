<?php

$tablename = "tbl_award_decent_work_values";

$options = array('comment' => 'Table to store decent work values.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
            'type' => 'text',
            'length' => 32,
            'notnull' => TRUE
            ),
	'categoryid' => array(
            'type' => 'text',
            'length' => 32,
            'notnull' => TRUE
            ),
	'label' => array(
            'type' => 'text',
            'length' => 128
            ),
        'value' => array(
            'type' => 'float',
            'length' => 8
            ),
        'unit' => array(
	   'type' => 'text',
	   'length' => 32
            ),
        'source' => array(
	   'type' => 'text',
	   'length' => 255
            ),
        'year' => array(
	   'type' => 'integer'
	    ),
        'notes' => array(
	   'type' => 'text',
	   'length' => 255
            )
	);
	
$name = 'tbl_award_decent_work_values_idx';
$indexes = array(
                'fields' => array(
                        'categoryid' => array()
                )
        );
?>