<?php
$tablename = "tbl_award_branch";

$options = array('comment' => 'table to store a list of branch values', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'partyid' => array(
	   'type' => 'text',
	   'length' => 32,
	   'notnull' => TRUE
	   ),
	'districtid' => array(
	   'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
	   ),
	'name' => array(
	   'type' => 'text',
	   'length' => 255
	   ),
  	'telephone' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'fax' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'url' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'email' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'addressline1' => array(
	   'type' => 'text',
	   'length' => 100
	   ),
	'addressline2' => array(
	   'type' => 'text',
	   'length' => 100
	   ),
	'postalline1' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'postaltown' => array(
	   'type' => 'text',
	   'length' => 50
	   ),
	'postalcode' => array(
	   'type' => 'text',
	   'length' => 4
	   )
	);
  
$name = 'tbl_award_branch_idx';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                	'partyid' => array(),
                	'districtid' => array()
                )
        );
?>